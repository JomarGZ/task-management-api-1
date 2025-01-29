<?php

namespace Database\Factories;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Models\Project;
use App\Models\Tenant;
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
        $startedAt = now();
        $completedAt = $startedAt->clone()->addDays(2);
        $deadlineAt = $completedAt->clone()->addDays(2);
        return [
            'tenant_id' => Tenant::factory(),
            'project_id' => Project::factory(),
            'assigned_dev_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->realText(500),
            'priority_level' => fake()->randomElement(PriorityLevel::cases())->value,
            'status' => fake()->randomElement(Statuses::cases())->value,
            'deadline_at' => $deadlineAt,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
        ];
    }
}
