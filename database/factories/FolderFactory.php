<?php

namespace Database\Factories;

use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

class FolderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Folder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->date('d-m-y'),
            'expires_in' => $this->faker->numberBetween(0, 100) * 24 * 3600,
            'active' => true,
            'is_encrypted' => false
        ];
    }
}
