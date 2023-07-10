<?php

use ASanikovich\LaravelSpatial\Geometry\Point;
use ASanikovich\LaravelSpatial\Tests\Custom\CustomPoint;

it('check serialisation of custom point', function (): void {
    $point = new CustomPoint(0, 180);

    $array = $point->toArray();

    expect($array)->toEqual(['type' => 'Point', 'coordinates' => [180.0, 0.0]])
        ->and(CustomPoint::fromArray($array))->toEqual($point);
});

it('check serialisation of point', function (): void {
    $point = new Point(0, 180);

    $array = $point->toArray();

    expect($array)->toEqual(['type' => 'Point', 'coordinates' => [180.0, 0.0]])
        ->and(Point::fromArray($array))->toEqual($point);
});
