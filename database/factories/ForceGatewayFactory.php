<?php

namespace Database\Factories;

use App\Models\ForceGateway;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForceGatewayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ForceGateway::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'source' => $this->faker->randomElement(['dakkeh', 'gisheh']),
            'gateway' => $this->faker->randomElement(['saman', 'mellat']),
        ];
    }
}
