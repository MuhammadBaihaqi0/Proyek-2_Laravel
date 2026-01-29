<?php

namespace Database\Factories;

use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TugasFactory extends Factory
{
    protected $model = Tugas::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'nama_tugas' => $this->faker->sentence(),
            'deskripsi' => $this->faker->paragraph(),
            'deadline' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
        ];
    }
}
