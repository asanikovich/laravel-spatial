<?php

use ASanikovich\LaravelSpatial\Enums\GeometryType;
use ASanikovich\LaravelSpatial\Exceptions\LaravelSpatialException;
use ASanikovich\LaravelSpatial\Geometry\Point;
use ASanikovich\LaravelSpatial\LaravelSpatialServiceProvider;
use ASanikovich\LaravelSpatial\Tests\Custom\CustomPointInvalid;

it('throws exception when invalid config', function (): void {
    config(['laravel-spatial.'.GeometryType::POINT->value => '']);

    expect(function (): void {
        $provider = new LaravelSpatialServiceProvider(app());
        $provider->boot();
    })->toThrow(LaravelSpatialException::class, 'Invalid class for geometry type "point", please check config');
});

it('throws exception when invalid class in config', function (): void {
    config(['laravel-spatial.'.GeometryType::POINT->value => CustomPointInvalid::class]);

    $error = sprintf(
        'Class for geometry type "point" should be instance of "%s" ("%s" provided), please check config',
        Point::class,
        CustomPointInvalid::class
    );

    expect(function (): void {
        $provider = new LaravelSpatialServiceProvider(app());
        $provider->boot();
    })->toThrow(LaravelSpatialException::class, $error);
});
