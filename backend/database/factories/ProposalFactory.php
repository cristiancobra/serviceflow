<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'date' => now()->toDateString(),
            'validity_days' => 30,
            'total_hours' => 10,
            'total_operational_cost' => 100,
            'total_third_party_cost' => 0,
            'total_profit' => 50,
            'total_profit_percentage' => 33.33,
            'total_discount' => 0,
            'total_price' => 150,
            'status' => 'sent',
        ];
    }
}
