<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Address> */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return ['user_id' => 1, 'label' => 'Casa', 'recipient' => fake()->name(), 'line1' => fake()->streetAddress(), 'city' => 'Ciudad de México', 'state' => 'CDMX', 'postal_code' => '06600', 'phone' => '5555555555', 'is_default' => true];
    }
}
