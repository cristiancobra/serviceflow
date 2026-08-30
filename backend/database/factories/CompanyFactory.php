<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    public function definition()
    {
        return [
            'legal_name' => $this->faker->company(),
            'business_name' => $this->faker->company(),
        ];
    }
}
