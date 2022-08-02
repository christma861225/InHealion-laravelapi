<?php

namespace App\Http\Controllers;

use App\Http\Resources\FolderResource;
use App\Models\Folder;
use App\Models\User;
use App\Models\UserPrivileges;
use App\Models\UserProfile;
use App\Utils\Files;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vinkla\Hashids\Facades\Hashids;

class FolderController extends Controller
{
    public function getAllByUserProfileId(Request $request, $userProfileId): JsonResponse
    {
        $user = $this->getUserFromProfile($userProfileId);
        if ($request->user()->cannot(UserPrivileges::VIEW_PROGRAMS, $user)) {
            $this->raiseError(403, "Resource not available");
        }
        return $this->respondWithResource(FolderResource::collection($user->folders));
    }

    public function get(Request $request, $id, $hash = null): JsonResponse
    {
        if($hash == null) {
            $user = $request->user();
        } else {
            $user = $this->getUserFromHash($hash);
        }

        $folder = $this->getFolderFromRequest($id);
        $this->canUserDownloadFolder($folder, $user);

        return $this->respondWithResource(new FolderResource($folder));
    }

    public function create(Request $request, $userProfileId): JsonResponse
    {
        if ($request->user()->cannot(UserPrivileges::MANAGE_PROGRAMS)) {
            $this->raiseError(403, "Resource not available");
        }

        $this->validate($request, [
            'name' => 'required|date_format:d-m-y|after:today'
        ]);

        $user = $this->getUserFromProfile($userProfileId);
        $name = $request->input('name');

        try {
            $folderFileName = Folder::getFolderPath($name, $user->id);
            if (!Storage::exists($folderFileName)) {
                Storage::makeDirectory($folderFileName);
            }
        } catch (\RunTimeException  $e) {
            Log::error($e->getMessage());
            $this->raiseError(500, "Cannot to make folder");
        }

        $folder = $user->folders()->where('name', $name)->first();
        if ($folder != null) {
            $this->raiseError(422, "Folder already exits");
        }

        $expiredDate = DateTime::createFromFormat('d-m-y', $name);
        $expiresIn = $expiredDate->getTimestamp() - (new DateTime())->getTimestamp();

        $folder = $user->folders()->create([
            'name' => $request->input('name'),
            'expires_in' => $expiresIn,
            'active' => true,
            'is_encrypted' => env('ENCRYPTION_ENABLED', true)
        ]);
        return $this->respondWithResource(new FolderResource($folder));
    }

    public function delete(Request $request, $id): JsonResponse
    {
        if ($request->user()->cannot(UserPrivileges::MANAGE_PROGRAMS)) {
            $this->raiseError(403, "Resource not available");
        }

        $folder = Folder::query()->whereKey(Hashids::decode($id))->first();
        if ($folder == null) {
            $this->raiseError(404, "Folder not found");
        }

        $folderFileName = $folder->path();

        $folder->programs()->delete();
        $folder->delete();

        if (Storage::exists($folderFileName)) {
            Storage::deleteDirectory($folderFileName);
        }
        return $this->respondWithMessage('Folder deleted');
    }

    public function import($id, $hash = null): BinaryFileResponse
    {
        $user = $this->getUserFromHash($hash);
        return $this->createZip($user, $id, false);
    }

    public function download(Request $request, $id): BinaryFileResponse
    {
        $user = $request->user();
        return $this->createZip($user, $id, true);
    }

    public function prepareDownload(Request $request, string $id): string
    {
        $folder = $this->getFolderFromRequest($id);
        $this->canUserDownloadFolder($folder, $request->user());

        $params = $request->user()->id . ':' . Carbon::now()->addMinutes(30)->timestamp;
        return base64_encode(Crypt::encrypt($params));
    }

    private function createZip(User $user, $id, $decryptFiles): BinaryFileResponse
    {
        $folder = $this->getFolderFromRequest($id);
        $this->canUserDownloadFolder($folder, $user);

        $path = storage_path('app/' . $folder->path());
        try {
            $files = collect($folder->programs)->map(function ($program) {
                return $program->name;
            });

            $zipFile = Files::makeZipWithFiles($folder->name, $path, $files->all(), $decryptFiles && $folder->is_encrypted);
            return response()->download($zipFile, $folder->name . '.zip', [
                'Content-Length' => filesize($zipFile),
                'Content-Type' => 'application/zip'
            ]);
        } catch (Exception $e) {
            $this->raiseError(500, $e->getMessage());
        }
    }

    private function getUserFromHash($hash): User
    {
        try {
            $decrypted = Crypt::decrypt(base64_decode($hash));
            list($userId, $expiredAt) = explode(":", $decrypted);
            if ($expiredAt < Carbon::now()->timestamp) {
                throw new Exception();
            }
            return User::find($userId);
        } catch (Exception $e) {
            $this->raiseError(422, "Request is not valid");
        }
    }

    private function canUserDownloadFolder(Folder $folder, User $user): bool
    {
        if ($user->cannot(UserPrivileges::VIEW_PROGRAMS, $folder->user)) {
            $this->raiseError(403, "Resource not available");
        }
        return true;
    }

    private function getFolderFromRequest($id): Folder
    {
        $folder = Folder::query()->whereKey(Hashids::decode($id))->first();
        if ($folder == null) {
            $this->raiseError(404, 'Folder not found');
        }
        return $folder;
    }

    private function getUserFromProfile($userProfileId): User
    {
        $userProfile = UserProfile::query()->whereKey(Hashids::decode($userProfileId))->first();
        if ($userProfile == null) {
            $this->raiseError(404, 'Resource not found');
        }

        return $userProfile->user;
    }
}
