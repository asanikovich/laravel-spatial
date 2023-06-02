<?php

namespace ASanikovich\LaravelSpatial\Tests\Custom;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomTestPlace>
 */
class CustomTestPlaceFactory extends Factory
{
    protected $model = CustomTestPlace::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName,
            'address' => $this->faker->address,
        ];
    }
}
