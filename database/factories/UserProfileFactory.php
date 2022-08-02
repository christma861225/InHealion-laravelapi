<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phone_number,
            'date_of_birth' => $this->faker->date()
        ];
    }
}
