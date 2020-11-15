<?php

namespace Database\Factories;

use App\Models\TransactionLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sales_id' =>  $this->faker->numberBetween(999, 999999),
            'price' => $this->faker->numberBetween(1000, 1500),
            'selected_gateway' => $this->faker->randomElement(['saman', 'mellat']),
            'final_gateway' => $this->faker->randomElement(['saman', 'mellat']),
            'source' => $this->faker->randomElement(['dakkeh', 'gisheh']),
            'status' =>$this->faker->randomElement(['init', 'successful' , 'failed']),
            'transaction_id'=> $this->faker->numberBetween(9999999, 99999999999),
            'error_message' => $this->faker->text(5000),
        ];
    }
}
