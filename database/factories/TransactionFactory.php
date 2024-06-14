<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Transaction;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'pairs' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'output' => $this->faker->randomFloat(0, 0, 9999999999.),
            'revenue' => $this->faker->randomFloat(0, 0, 9999999999.),
            'status' => $this->faker->randomElement(["Waiting","Processing","Done","Failed"]),
            'customer_id' => Customer::factory(),
        ];
    }
}
