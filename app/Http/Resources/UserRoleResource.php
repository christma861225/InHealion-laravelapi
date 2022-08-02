<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string name
 * @method privileges()
 */
class UserRoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'privileges' => UserPrivilegesResource::collection($this->privileges()->get())
        ];
    }
}
