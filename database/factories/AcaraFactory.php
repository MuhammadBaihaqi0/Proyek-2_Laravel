<?php

namespace Database\Factories;

use App\Models\Acara;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcaraFactory extends Factory
{
    protected $model = Acara::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'nama_acara' => $this->faker->sentence(),
            'tanggal' => $this->faker->dateTimeBetween('+1 day', '+60 days'),
        ];
    }
}
