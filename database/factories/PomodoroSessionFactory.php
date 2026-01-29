<?php

namespace Database\Factories;

use App\Models\PomodoroSession;
use App\Models\User;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Factories\Factory;

class PomodoroSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PomodoroSession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startedAt = $this->faker->dateTimeThisMonth();

        return [
            'user_id' => User::factory(),
            'task_id' => Tugas::factory(),
            'started_at' => $startedAt,
            'ended_at' => now(\DateTime::ATOM)->modify('+25 minutes'),
            'duration_seconds' => 1500, // 25 minutes
            'type' => $this->faker->randomElement(['work', 'break']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
