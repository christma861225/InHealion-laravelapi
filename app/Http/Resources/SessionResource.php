<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string token
 * @property mixed expires_in
 * @property mixed created_at
 *
 */
class SessionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'token' => $this->token,
            'expires_in' => $this->expires_in,
            'created_at' => $this->created_at,
        ];
    }
}
