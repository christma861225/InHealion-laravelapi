<?php


namespace App\Http\Controllers;

use App\Http\Resources\FirmwareResource;
use App\Models\Firmware;
use App\Models\FirmwareFiles;
use App\Models\UserPrivileges;
use App\Utils\Files;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vinkla\Hashids\Facades\Hashids;
use function App\Utils\makeZipWithFiles;

class FirmwareController extends Controller
{
    private $firmwarePath;

    public function __construct()
    {
        $this->firmwarePath = env('FIRMWARE_PATH');
    }

    public function getAll(Request $request): JsonResponse
    {
        // if ($request->user() == null || $request->user()->cannot(UserPrivileges::MANAGE_FIRMWARE)) {
        //     $firmwares = Firmware::where('active', true)->get();
        // } else {
        //     $firmwares = Firmware::all();
        // }
        $firmwares = Firmware::all();
        return $this->respondWithResource(FirmwareResource::collection($firmwares));
    }

    public function getLatest(): JsonResponse
    {
        $firmwares = collect(Firmware::all())->sortByDesc(function ($firmware) {
            return intval(str_replace(".", "", $firmware->version));
        });

        if ($firmwares->count() == 0) {
            $this->raiseError(404, "Firmware not found");
        }
        $forceUpdateFirmware = $firmwares->firstWhere('active', 1);
        $firmware = $forceUpdateFirmware != null ? $forceUpdateFirmware : $firmwares->first();

        return $this->respondWithResource(new FirmwareResource($firmware));
    }

    public function update(Request $request, $id): JsonResponse
    {
        if ($request->user()->cannot(UserPrivileges::MANAGE_FIRMWARE)) {
            $this->raiseError(403, "Resource not available");
        }

        $firmware = Firmware::where('id', Hashids::decode($id))->first();
        if ($firmware == null) {
            $this->raiseError(404, "Firmware not found");
        }

        $this->validate($request, [
            'version' => 'nullable|regex:/^\d+\.\d+\.\d+$/',
            'active' => 'nullable|boolean',
        ]);

        $fields = collect($request->only(['version', 'active']))->filter(function ($item) {
            return $item !== null;
        })->toArray();

        if (array_key_exists('active', $fields) && $fields['active']) {
            Firmware::where('active', 1)->update(['active'=>0]);
        }

        $firmware->update($fields);
        return $this->respondWithResource(new FirmwareResource($firmware));
    }

    public function download($version): BinaryFileResponse
    {
        $firmware = Firmware::where('version', $version)->first();
        if ($firmware == null) {
            $this->raiseError(404, "Firmware not found");
        }

        try {
            $path = storage_path('app/' . $firmware->path());
            $files = collect($firmware->files)->map(function ($file) {
                return $file->file_name;
            })->toArray();
            $zipFile = Files::makeZipWithFiles($firmware->version, $path, $files);
            return response()->download($zipFile, 'firmware_v' . str_replace('.', '-', $firmware->version) . '.zip', [
                'Content-Length' => filesize($zipFile),
                'Content-Type' => 'application/zip'
            ]);
        } catch (Exception $e) {
            $this->raiseError(500, $e->getMessage());
        }
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
            'version' => 'required|regex:/^\d+\.\d+\.\d+$/',
            'active' => 'nullable|boolean',
            'files' => 'required|array',
            'files.*' => 'required|file'
        ]);

        $version = $request->input('version');
        $firmware = Firmware::where('version', $version)->first();
        if ($firmware != null) {
            $this->raiseError(422, "Version $version is already uploaded");
        }

        $files = $request->file('files');
        $directory = Firmware::getPath($version);

        $this->storeOnDisk($directory, $files);

        try {
            DB::beginTransaction();
            $firmware = Firmware::create([
                'version' => $request->input('version'),
                'active' => $request->input('active', false)
            ]);

            $firmware->files()->saveMany(collect($files)->map(function ($file) use ($directory) {
                $name = $file->getClientOriginalName();
                return new FirmwareFiles([
                    'file_name' => $name,
                    'hash' => hash_file("sha256", storage_path('app/' . $directory . '/' . $name))
                ]);
            }));
            DB::commit();
            return $this->respondWithResource(new FirmwareResource($firmware));
        } catch (Exception  $e) {
            DB::rollBack();
            Log::error($e);
            $this->raiseError(500, "Cannot to create firmware");
        }
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

        $firmware = Firmware::where('version', $version)->first();
        if ($firmware == null) {
            $this->raiseError(404, "Firmware not found");
        }

        try {
            DB::beginTransaction();
            $firmware->files()->delete();
            $firmware->delete();
            Storage::deleteDirectory($firmware->path());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->raiseError(500, "Cannot to delete firmware");
        }

        return $this->respondWithMessage('Firmware deleted');
    }

    /**
     * @param string $directory
     * @param UploadedFile[] $files
     */
    private function storeOnDisk(string $directory, array $files)
    {
        try {
            if (Storage::exists($directory)) {
                Storage::deleteDirectory($directory);
            }
            Storage::makeDirectory($directory);

            foreach ($files as $file) {
                $file->storeAs($directory, $file->getClientOriginalName());
            }
        } catch (Exception  $e) {
            Log::error($e->getMessage());
            $this->raiseError(500, "Cannot to store firmware");
        }
    }

//    private function parse(string $xml, string $fileName): CpuFirmware
//    {
//        $element = new SimpleXMLElement($xml);
//
//        return new CpuFirmware(
//            $fileName,
//            (string)$element->fw->version,
//            DateTime::createFromFormat('d/m/Y h:i:s A', (string)$element->fw->date),
//            (string)$element->fw->device
//        );
//    }
}
