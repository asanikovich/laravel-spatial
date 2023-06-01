<?php

namespace ASanikovich\LaravelSpatial\Tests\Database\TestFactories;

use ASanikovich\LaravelSpatial\Tests\Database\TestModels\TestPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestPlace>
 */
class TestPlaceFactory extends Factory
{
    protected $model = TestPlace::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName,
            'address' => $this->faker->address,
        ];
    }
}
