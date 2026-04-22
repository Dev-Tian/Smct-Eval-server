<?php

namespace Database\Factories;

use App\Models\SectionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubSection>
 */
class SubSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_id'   =>  SectionType::all()->random()->id,
            'section'   =>  $this->faker->word(),
        ];
    }
}
