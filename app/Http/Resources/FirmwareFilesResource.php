<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string file_name
 */
class FirmwareFilesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->file_name
        ];
    }
}
