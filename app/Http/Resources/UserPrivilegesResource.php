<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string name
 */
class UserPrivilegesResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->name;
    }
}
