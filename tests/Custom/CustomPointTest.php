<?php

use ASanikovich\LaravelSpatial\Enums\GeometryType;
use ASanikovich\LaravelSpatial\Geometry\Point;
use ASanikovich\LaravelSpatial\Tests\Custom\CustomPoint;
use ASanikovich\LaravelSpatial\Tests\Custom\CustomPointConfig;
use ASanikovich\LaravelSpatial\Tests\Custom\CustomTestPlace;
use ASanikovich\LaravelSpatial\Tests\Database\TestModels\TestPlace;
use Illuminate\Foundation\Testing\DatabaseTruncation;

uses(DatabaseTruncation::class);

it('creates a model record with custom point', function (): void {
    $point = new CustomPoint(0, 180);

    /** @var CustomTestPlace $testPlace */
    $testPlace = CustomTestPlace::factory()->create(['point' => $point]);

    expect($testPlace->point)->toBeInstanceOf(CustomPoint::class)
        ->and($testPlace->point)->toEqual($point);

    /** @var CustomTestPlace $testPlace */
    $testPlace = CustomTestPlace::find(1);
    expect($testPlace->point)->toBeInstanceOf(CustomPoint::class)
        ->and($testPlace->point)->toEqual($point);
});

it('creates a model record with custom point based on config', function (): void {
    config(['laravel-spatial.'.GeometryType::POINT->value => CustomPointConfig::class]);

    $point = new Point(0, 180);

    /** @var TestPlace $testPlace */
    $testPlace = TestPlace::factory()->create(['point' => $point]);

    expect($testPlace->point)->toBeInstanceOf(Point::class)
        ->and($testPlace->point)->toEqual($point);

    /** @var TestPlace $testPlace */
    $testPlace = TestPlace::find(1);
    expect($testPlace->point)->toBeInstanceOf(CustomPointConfig::class);
});

it('creates a model record with custom point override config', function (): void {
    config(['laravel-spatial.'.GeometryType::POINT->value => CustomPointConfig::class]);

    $point = new CustomPoint(0, 180);

    /** @var CustomTestPlace $testPlace */
    $testPlace = CustomTestPlace::factory()->create(['point' => $point]);

    expect($testPlace->point)->toBeInstanceOf(CustomPoint::class)
        ->and($testPlace->point)->toEqual($point);

    /** @var CustomTestPlace $testPlace */
    $testPlace = CustomTestPlace::find(1);
    expect($testPlace->point)->toBeInstanceOf(CustomPoint::class);
});
