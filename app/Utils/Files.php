<?php

namespace App\Utils;

use App\Http\Controllers\ProgramController;
use App\Models\Firmware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class Files
{
    private const KEY = "\x3a\xf5\x4c\x68\xaa\x0a\x65\xf2\xb2\x2f\xd5\x33\x05\xb9\xad\x96";
    private const IV = "\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0";
    private const CYPHER = "AES-128-CBC";

    /**
     * @param string $name
     * @param string $path
     * @param array $files
     * @param bool $decrypt
     * @return string
     * @throws \Exception
     */
    public static function makeZipWithFiles(string $name, string $path, array $files, bool $decrypt = false): string
    {
        $zip = new \ZipArchive();
        $tempFileUri = $path . '/' . $name . '.zip';
        if (file_exists($tempFileUri)) unlink($tempFileUri);

        Log::info('Create zip archive: ' . $tempFileUri);
        if ($zip->open($tempFileUri, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            // Add File in ZipArchive
            foreach ($files as $file) {
                $fileName = $path . '/' . $file;
                if (!file_exists($fileName)) continue;

                Log::info("Add $fileName to $tempFileUri archive");
                if ($decrypt) {
                    $content = self::decryptFile($fileName);
                    $result = $zip->addFromString($file, $content);
                } else {
                    $result = $zip->addFile($fileName, $file);
                }
                if (!$result) {
                    throw new \Exception('Could not add file to ZIP: ' . $file);
                }
            }
            // Close ZipArchive
            $zip->close();
        } else {
            throw new \Exception('Could not open ZIP file.');
        }
        return $tempFileUri;
    }

    public static function encryptData(string $data): string
    {
        $total = ceil(strlen($data) / 16) * 16;
        $content = str_pad($data, $total, "\0");
        return openssl_encrypt($content, self::CYPHER, self::KEY, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, self::IV);
    }

    public static function decryptFile(string $path): string
    {
        $content = file_get_contents($path);
        $decrypted = openssl_decrypt($content, self::CYPHER, self::KEY, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, self::IV);
        return rtrim($decrypted, "\0");
    }
}
