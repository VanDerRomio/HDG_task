<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::query()
            ->select('id')
            ->inRandomOrder()
            ->first();

        return [
            'user_id'       => $user?->id ?? User::factory()->create(),
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
            'status'        => fake()->randomElement(TaskStatus::valuesAsArray()),
        ];
    }
}
