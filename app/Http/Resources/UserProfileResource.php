<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property integer id
 * @property string name
 * @property string surname
 * @property string email
 * @property string address
 * @property string phoneMumber
 * @property mixed date_of_birth
 * @property mixed created_at
 * @property mixed updated_at
 */
class UserProfileResource extends JsonResource
{

    public function toArray($request): array
    {
        $collection = collect([
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'phoneNumber' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ])->filter(function ($value, $key) {
            return $value !== null;
        });
        return $collection->toArray();
    }
}
