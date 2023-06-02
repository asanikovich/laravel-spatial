<?php

namespace ASanikovich\LaravelSpatial\Tests;

use ASanikovich\LaravelSpatial\LaravelSpatialServiceProvider;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
    }

    /**
     * @return class-string<ServiceProvider>[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelSpatialServiceProvider::class,
        ];
    }
}
