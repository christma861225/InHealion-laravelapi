<?php

namespace App\Http\Resources;

use App\Models\FirmwareFiles;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property integer id
 * @property string version
 * @property mixed created_at
 * @property FirmwareFiles[] files
 * @property mixed active
 * @method files()
 */
class FirmwareResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'active' => $this->active,
            'version' => $this->version,
            'files' => FirmwareFilesResource::collection($this->files),
            'created_at' => $this->created_at
        ];
    }
}
