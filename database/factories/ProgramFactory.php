<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Program::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'program_' . $this->faker->numberBetween(),
            'hash' => $this->faker->unique()->macAddress,
            'active' => true
        ];
    }
}
