<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UsersEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'completed']);
        $probationary = $this->faker->boolean();

        return [
            'employee_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'evaluator_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'category' => $this->faker->sentence(5),
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'status' => $status,
            'coverageFrom' => $this->faker->date(),
            'coverageTo' => $this->faker->date(),

            'reviewTypeProbationary' => $probationary
                ? $this->faker->randomElement([3, 5])
                : null,
            'reviewTypeRegular' => $probationary
                ? null
                : $this->faker->randomElement(['Q1', 'Q2', 'Q3', 'Q4']),

            'reviewTypeOthersImprovement' => $this->faker->boolean(),
            'reviewTypeOthersCustom' => $this->faker->optional()->sentence(),
            'priorityArea1' => $this->faker->optional()->sentence(3),
            'priorityArea2' => $this->faker->optional()->sentence(3),
            'priorityArea3' => $this->faker->optional()->sentence(3),
            'remarks' => $this->faker->optional()->paragraph(),
            'overallComments' => $this->faker->optional()->paragraph(),
            'evaluatorApprovedAt' => $this->faker->date(),
            'employeeApprovedAt' => $status === 'completed'
                ? $this->faker->date()
                : null,
        ];
    }
}
