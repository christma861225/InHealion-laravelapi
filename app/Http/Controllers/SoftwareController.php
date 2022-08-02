<?php


namespace App\Http\Controllers;

use App\Http\Resources\SoftwareResource;
use App\Models\Software;
use App\Models\UserPrivileges;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vinkla\Hashids\Facades\Hashids;
use function App\Utils\makeZipWithFiles;

class SoftwareController extends Controller
{
    private $path;

    public function __construct()
    {
        $this->path = env('SOFTWARE_PATH');
    }

    public function getAll(Request $request): JsonResponse
    {
        if ($request->user() == null || $request->user()->cannot(UserPrivileges::MANAGE_FIRMWARE)) {
            $softwares = Software::where('active', true)->get();
        } else {
            $softwares = Software::all();
        }
        return $this->respondWithResource(SoftwareResource::collection($softwares));
    }

    public function getLatest(): JsonResponse
    {
        $softwares = collect(Software::where('active', 1)->get())->sortByDesc(function ($firmware) {
            return intval(str_replace(".", "", $firmware->version));
        });

        if ($softwares->count() == 0) {
            $this->raiseError(404, "Software not found");
        }
        return $this->respondWithResource(new SoftwareResource($softwares[0]));
    }

    public function update(Request $request, $id): JsonResponse
    {
        if ($request->user()->cannot(UserPrivileges::MANAGE_FIRMWARE)) {
            $this->raiseError(403, "Resource not available");
        }

        $software = Software::where('id', Hashids::decode($id))->first();
        if ($software == null) {
            $this->raiseError(404, "Software not found");
        }

        $this->validate($request, [
            'version' => 'nullable|regex:/^\d+\.\d+\.\d+(\.\d+)?$/',
            'active' => 'nullable|boolean',
        ]);

        $fields = collect($request->only(['version', 'active']))->filter(function ($item) {
            return $item !== null;
        })->toArray();

        $software->update($fields);
        return $this->respondWithResource(new SoftwareResource($software));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        if ($request->user()->cannot(UserPrivileges::MANAGE_FIRMWARE)) {
            $this->raiseError(403, "Resource not available");
        }

        $this->validate($request, [
            'version' => 'required|regex:/^\d+\.\d+\.\d+(\.\d+)?$/',
            'file' => 'required|file'
        ]);

        $version = $request->input('version');
        $software = Software::where('version', $version)->first();
        if ($software != null) {
            $this->raiseError(422, "Version $version is already uploaded");
        }

        $file = $request->file('file');
        $directory = Software::getPath($version);

        $this->storeOnDisk($directory, $file);

        $software = Software::create(['version' => $request->input('version'), 'active' => true, 'file' => $file->getClientOriginalName()]);
        return $this->respondWithResource(new SoftwareResource($software));
    }

    public function download($version): StreamedResponse
    {
        $software = Software::where('version', $version)->first();
        if ($software == null) {
            $this->raiseError(404, "Software not found");
        }

        return Storage::download($software->path() . '/' . $software->file);
    }

    /**
     * @param Request $request
     * @param $version
     * @return JsonResponse
     */
    public function delete(Request $request, $version): JsonResponse
    {
        if ($request->user()->cannot(UserPrivileges::MANAGE_FIRMWARE)) {
            $this->raiseError(403, "Resource not available");
        }

        $software = Software::where('version', $version)->first();
        if ($software == null) {
            $this->raiseError(404, "Software not found");
        }

        try {
            DB::beginTransaction();
            $software->delete();
            Storage::deleteDirectory($software->path());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->raiseError(500, "Cannot to delete software");
        }

        return $this->respondWithMessage('Software deleted');
    }

    /**
     * @param string $directory
     * @param UploadedFile[] $files
     */
    private function storeOnDisk(string $directory, UploadedFile $file)
    {
        try {
            if (Storage::exists($directory)) {
                Storage::deleteDirectory($directory);
            }
            Storage::makeDirectory($directory);
            $file->storeAs($directory, $file->getClientOriginalName());
        } catch (Exception  $e) {
            Log::error($e->getMessage());
            $this->raiseError(500, "Cannot to store software");
        }
    }
}
