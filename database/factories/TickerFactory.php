<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Network;
use App\Models\Ticker;

class TickerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticker::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'icon' => $this->faker->word(),
            'symbol' => $this->faker->word(),
            'ids' => $this->faker->regexify('[A-Za-z0-9]{50}'),
            'minimum' => $this->faker->randomFloat(0, 0, 9999999999.),
            'usd_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'status' => $this->faker->randomElement(["Active","Disabled"]),
            'network_id' => Network::factory(),
        ];
    }
}
