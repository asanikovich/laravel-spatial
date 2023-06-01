<?php

use ASanikovich\LaravelSpatial\Enums\Srid;
use ASanikovich\LaravelSpatial\Geometry\LineString;
use ASanikovich\LaravelSpatial\Geometry\Point;
use ASanikovich\LaravelSpatial\Geometry\Polygon;
use ASanikovich\LaravelSpatial\Tests\Database\TestModels\TestPlace;
use Illuminate\Support\Facades\DB;

uses(getDatabaseTruncationClass());

it('calculates distance', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::query()->select(['id'])->selectRaw(DB::raw('id as id_new'))
        ->withDistance('point', new Point(1, 1, Srid::WGS84->value))
        ->firstOrFail();

    expect($testPlaceWithDistance->distance)->toBe(156897.79947260793)
        ->and($testPlaceWithDistance->id)->toBe(1)
        ->and($testPlaceWithDistance->id_new)->toBe(1);
})->skip(fn () => ! isSupportAxisOrder());

it('calculates distance - without axis-order', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistance('point', new Point(1, 1, Srid::WGS84->value))->firstOrFail();

    expect($testPlaceWithDistance->distance)->toBe(1.4142135623730951);
})->skip(fn () => isSupportAxisOrder());

it('calculates distance with alias', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistance('point', new Point(1, 1, Srid::WGS84->value), 'distance_in_meters')
        ->firstOrFail();

    expect($testPlaceWithDistance->distance_in_meters)->toBe(156897.79947260793);
})->skip(fn () => ! isSupportAxisOrder());

it('calculates distance with alias - without axis-order', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistance('point', new Point(1, 1, Srid::WGS84->value), 'distance_in_meters')
        ->firstOrFail();

    expect($testPlaceWithDistance->distance_in_meters)->toBe(1.4142135623730951);
})->skip(fn () => isSupportAxisOrder());

it('filters by distance', function (): void {
    $pointWithinDistance = new Point(0, 0, Srid::WGS84->value);
    $pointNotWithinDistance = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointWithinDistance]);
    TestPlace::factory()->create(['point' => $pointNotWithinDistance]);

    /** @var TestPlace[] $testPlacesWithinDistance */
    $testPlacesWithinDistance = TestPlace::whereDistance('point', new Point(1, 1, Srid::WGS84->value), '<', 200_000)
        ->get();

    expect($testPlacesWithinDistance)->toHaveCount(1)
        ->and($testPlacesWithinDistance[0]->point)->toEqual($pointWithinDistance);
})->skip(fn () => ! isSupportAxisOrder());

it('filters by distance - without axis-order', function (): void {
    $pointWithinDistance = new Point(0, 0, Srid::WGS84->value);
    $pointNotWithinDistance = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointWithinDistance]);
    TestPlace::factory()->create(['point' => $pointNotWithinDistance]);

    /** @var TestPlace[] $testPlacesWithinDistance */
    $testPlacesWithinDistance = TestPlace::whereDistance('point', new Point(1, 1, Srid::WGS84->value), '<', 2)->get();

    expect($testPlacesWithinDistance)->toHaveCount(1)
        ->and($testPlacesWithinDistance[0]->point)->toEqual($pointWithinDistance);
})->skip(fn () => isSupportAxisOrder());

it('orders by distance ASC', function (): void {
    $closerTestPlace = TestPlace::factory()->create(['point' => new Point(1, 1, Srid::WGS84->value)]);
    $fartherTestPlace = TestPlace::factory()->create(['point' => new Point(2, 2, Srid::WGS84->value)]);

    /** @var TestPlace[] $testPlacesOrderedByDistance */
    $testPlacesOrderedByDistance = TestPlace::orderByDistance('point', new Point(0, 0, Srid::WGS84->value))->get();

    expect($testPlacesOrderedByDistance[0]->id)->toBe($closerTestPlace->id)
        ->and($testPlacesOrderedByDistance[1]->id)->toBe($fartherTestPlace->id);
});

it('orders by distance DESC', function (): void {
    $closerTestPlace = TestPlace::factory()->create(['point' => new Point(1, 1, Srid::WGS84->value)]);
    $fartherTestPlace = TestPlace::factory()->create(['point' => new Point(2, 2, Srid::WGS84->value)]);

    /** @var TestPlace[] $testPlacesOrderedByDistance */
    $testPlacesOrderedByDistance = TestPlace::orderByDistance('point', new Point(0, 0, Srid::WGS84->value), 'desc')
        ->get();

    expect($testPlacesOrderedByDistance[1]->id)->toBe($closerTestPlace->id)
        ->and($testPlacesOrderedByDistance[0]->id)->toBe($fartherTestPlace->id);
});

it('calculates distance sphere', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistanceSphere('point', new Point(1, 1, Srid::WGS84->value))->firstOrFail();

    expect($testPlaceWithDistance->distance)->toBe(157249.59776850493)
        ->and($testPlaceWithDistance->name)->not()->toBeNull();
})->skip(fn () => ! isSupportAxisOrder());

it('calculates distance sphere - without axis-order', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistanceSphere('point', new Point(1, 1, Srid::WGS84->value))->firstOrFail();

    expect($testPlaceWithDistance->distance)->toBe(157249.0357231545)
        ->and($testPlaceWithDistance->name)->not()->toBeNull();
})->skip(fn () => isSupportAxisOrder());

it('calculates distance sphere with alias', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    $point = new Point(1, 1, Srid::WGS84->value);
    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistanceSphere('point', $point, 'distance_in_meters')->firstOrFail();

    expect($testPlaceWithDistance->distance_in_meters)->toBe(157249.59776850493);
})->skip(fn () => ! isSupportAxisOrder());

it('calculates distance sphere with alias - without axis-order', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    $point = new Point(1, 1, Srid::WGS84->value);
    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistanceSphere('point', $point, 'distance_in_meters')->firstOrFail();

    expect($testPlaceWithDistance->distance_in_meters)->toBe(157249.0357231545);
})->skip(fn () => isSupportAxisOrder());

it('filters distance sphere', function (): void {
    $pointWithinDistance = new Point(0, 0, Srid::WGS84->value);
    $pointNotWithinDistance = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointWithinDistance]);
    TestPlace::factory()->create(['point' => $pointNotWithinDistance]);

    $point = new Point(1, 1, Srid::WGS84->value);
    /** @var TestPlace[] $testPlacesWithinDistance */
    $testPlacesWithinDistance = TestPlace::whereDistanceSphere('point', $point, '<', 200000)->get();

    expect($testPlacesWithinDistance)->toHaveCount(1)
        ->and($testPlacesWithinDistance[0]->point)->toEqual($pointWithinDistance);
});

it('orders by distance sphere ASC', function (): void {
    $closerTestPlace = TestPlace::factory()->create(['point' => new Point(1, 1, Srid::WGS84->value)]);
    $fartherTestPlace = TestPlace::factory()->create(['point' => new Point(2, 2, Srid::WGS84->value)]);

    /** @var TestPlace[] $testPlacesOrderedByDistance */
    $testPlacesOrderedByDistance = TestPlace::orderByDistanceSphere('point', new Point(0, 0, Srid::WGS84->value))
        ->get();

    expect($testPlacesOrderedByDistance[0]->id)->toBe($closerTestPlace->id)
        ->and($testPlacesOrderedByDistance[1]->id)->toBe($fartherTestPlace->id);
});

it('orders by distance sphere DESC', function (): void {
    $closerTestPlace = TestPlace::factory()->create(['point' => new Point(1, 1, Srid::WGS84->value)]);
    $fartherTestPlace = TestPlace::factory()->create(['point' => new Point(2, 2, Srid::WGS84->value)]);

    $point = new Point(0, 0, Srid::WGS84->value);
    /** @var TestPlace[] $testPlacesOrderedByDistance */
    $testPlacesOrderedByDistance = TestPlace::orderByDistanceSphere('point', $point, 'desc')->get();

    expect($testPlacesOrderedByDistance[1]->id)->toBe($closerTestPlace->id)
        ->and($testPlacesOrderedByDistance[0]->id)->toBe($fartherTestPlace->id);
});

it('filters by within', function (): void {
    $polygon = Polygon::fromJson(
        '{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}',
        Srid::WGS84->value
    );
    $pointWithinPolygon = new Point(0, 0, Srid::WGS84->value);
    $pointOutsidePolygon = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointWithinPolygon]);
    TestPlace::factory()->create(['point' => $pointOutsidePolygon]);

    /** @var TestPlace[] $testPlacesWithinPolygon */
    $testPlacesWithinPolygon = TestPlace::whereWithin('point', $polygon)->get();

    expect($testPlacesWithinPolygon)->toHaveCount(1)
        ->and($testPlacesWithinPolygon[0]->point)->toEqual($pointWithinPolygon);
});

it('filters by not within', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}',
        Srid::WGS84->value);
    $pointWithinPolygon = new Point(0, 0, Srid::WGS84->value);
    $pointOutsidePolygon = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointWithinPolygon]);
    TestPlace::factory()->create(['point' => $pointOutsidePolygon]);

    /** @var TestPlace[] $testPlacesNotWithinPolygon */
    $testPlacesNotWithinPolygon = TestPlace::whereNotWithin('point', $polygon)->get();

    expect($testPlacesNotWithinPolygon)->toHaveCount(1)
        ->and($testPlacesNotWithinPolygon[0]->point)->toEqual($pointOutsidePolygon);
});

it('filters by contains', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}',
        Srid::WGS84->value);
    $pointWithinPolygon = new Point(0, 0, Srid::WGS84->value);
    $pointOutsidePolygon = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['polygon' => $polygon]);

    $testPlace = TestPlace::whereContains('polygon', $pointWithinPolygon)->first();
    $testPlace2 = TestPlace::whereContains('polygon', $pointOutsidePolygon)->first();

    expect($testPlace)->not->toBeNull()
        ->and($testPlace2)->toBeNull();
});

it('filters by not contains', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}',
        Srid::WGS84->value);
    $pointWithinPolygon = new Point(0, 0, Srid::WGS84->value);
    $pointOutsidePolygon = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['polygon' => $polygon]);

    $testPlace = TestPlace::whereNotContains('polygon', $pointWithinPolygon)->first();
    $testPlace2 = TestPlace::whereNotContains('polygon', $pointOutsidePolygon)->first();

    expect($testPlace)->toBeNull()
        ->and($testPlace2)->not->toBeNull();
});

it('filters by touches', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[0,-1],[0,0],[-1,0],[-1,-1]]]}',
        Srid::WGS84->value);
    $pointTouchesPolygon = new Point(0, 0, Srid::WGS84->value);
    $pointNotTouchesPolygon = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointTouchesPolygon]);
    TestPlace::factory()->create(['point' => $pointNotTouchesPolygon]);

    /** @var TestPlace[] $testPlacesTouchPolygon */
    $testPlacesTouchPolygon = TestPlace::whereTouches('point', $polygon)->get();

    expect($testPlacesTouchPolygon)->toHaveCount(1)
        ->and($testPlacesTouchPolygon[0]->point)->toEqual($pointTouchesPolygon);
});

it('filters by intersects', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}',
        Srid::WGS84->value);
    $pointIntersectsPolygon = new Point(0, 0, Srid::WGS84->value);
    $pointNotIntersectsPolygon = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointIntersectsPolygon]);
    TestPlace::factory()->create(['point' => $pointNotIntersectsPolygon]);

    /** @var TestPlace[] $testPlacesInterestPolygon */
    $testPlacesInterestPolygon = TestPlace::whereIntersects('point', $polygon)->get();

    expect($testPlacesInterestPolygon)->toHaveCount(1)
        ->and($testPlacesInterestPolygon[0]->point)->toEqual($pointIntersectsPolygon);
});

it('filters by crosses', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}',
        Srid::WGS84->value);
    $lineStringCrossesPolygon = LineString::fromJson('{"type":"LineString","coordinates":[[0,0],[2,0]]}',
        Srid::WGS84->value);
    $lineStringNotCrossesPolygon = LineString::fromJson('{"type":"LineString","coordinates":[[50,50],[52,50]]}',
        Srid::WGS84->value);
    TestPlace::factory()->create(['line_string' => $lineStringCrossesPolygon]);
    TestPlace::factory()->create(['line_string' => $lineStringNotCrossesPolygon]);

    /** @var TestPlace[] $testPlacesCrossPolygon */
    $testPlacesCrossPolygon = TestPlace::whereCrosses('line_string', $polygon)->get();

    expect($testPlacesCrossPolygon)->toHaveCount(1)
        ->and($testPlacesCrossPolygon[0]->line_string)->toEqual($lineStringCrossesPolygon);
});

it('filters by disjoint', function (): void {
    $polygon = Polygon::fromJson(
        '{"type":"Polygon","coordinates":[[[-1,-1],[-0.5,-1],[-0.5,-0.5],[-1,-0.5],[-1,-1]]]}',
        Srid::WGS84->value
    );
    $pointDisjointsPolygon = new Point(0, 0, Srid::WGS84->value);
    $pointNotDisjointsPolygon = new Point(-1, -1, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $pointDisjointsPolygon]);
    TestPlace::factory()->create(['point' => $pointNotDisjointsPolygon]);

    /** @var TestPlace[] $testPlacesDisjointPolygon */
    $testPlacesDisjointPolygon = TestPlace::whereDisjoint('point', $polygon)->get();

    expect($testPlacesDisjointPolygon)->toHaveCount(1)
        ->and($testPlacesDisjointPolygon[0]->point)->toEqual($pointDisjointsPolygon);
});

it('filters by overlaps', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-0.75,-0.75],[1,-1],[1,1],[-1,1],[-0.75,-0.75]]]}',
        Srid::WGS84->value);
    $overlappingPolygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[-0.5,-1],[-0.5,-0.5],[-1,-0.5],[-1,-1]]]}',
        Srid::WGS84->value);
    $notOverlappingPolygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-10,-10],[-5,-10],[-5,-5],[-10,-5],[-10,-10]]]}',
        Srid::WGS84->value);
    TestPlace::factory()->create(['polygon' => $overlappingPolygon]);
    TestPlace::factory()->create(['polygon' => $notOverlappingPolygon]);

    /** @var TestPlace[] $overlappingTestPlaces */
    $overlappingTestPlaces = TestPlace::whereOverlaps('polygon', $polygon)->get();

    expect($overlappingTestPlaces)->toHaveCount(1)
        ->and($overlappingTestPlaces[0]->polygon)->toEqual($overlappingPolygon);
});

it('filters by equals', function (): void {
    $point1 = new Point(0, 0, Srid::WGS84->value);
    $point2 = new Point(50, 50, Srid::WGS84->value);
    TestPlace::factory()->create(['point' => $point1]);
    TestPlace::factory()->create(['point' => $point2]);

    /** @var TestPlace[] $testPlaces */
    $testPlaces = TestPlace::whereEquals('point', $point1)->get();

    expect($testPlaces)->toHaveCount(1)
        ->and($testPlaces[0]->point)->toEqual($point1);
});

it('filters by SRID', function (): void {
    $point1 = new Point(0, 0, Srid::WGS84->value);
    $point2 = new Point(50, 50, 0);
    TestPlace::factory()->create(['point' => $point1]);
    TestPlace::factory()->create(['point' => $point2]);

    /** @var TestPlace[] $testPlaces */
    $testPlaces = TestPlace::whereSrid('point', '=', Srid::WGS84->value)->get();

    expect($testPlaces)->toHaveCount(1)
        ->and($testPlaces[0]->point)->toEqual($point1);
});

it('uses spatial function with column', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistance('point', 'point')->firstOrFail();

    expect($testPlaceWithDistance->distance)->toBe(0.0)
        ->and($testPlaceWithDistance->name)->not()->toBeNull();
});

it('uses spatial function with column that contains table name', function (): void {
    TestPlace::factory()->create(['point' => new Point(0, 0, Srid::WGS84->value)]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::withDistance('test_places.point', 'test_places.point')->firstOrFail();

    expect($testPlaceWithDistance->distance)->toBe(0.0)
        ->and($testPlaceWithDistance->name)->not()->toBeNull();
});

it('uses spatial function with expression', function (): void {
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}');
    TestPlace::factory()->create([
        'polygon' => $polygon,
        'longitude' => 0,
        'latitude' => 0,
    ]);

    /** @var TestPlace $testPlaceWithDistance */
    $testPlaceWithDistance = TestPlace::whereWithin(DB::raw('POINT(longitude, latitude)'), DB::raw('polygon'))
        ->firstOrFail();

    expect($testPlaceWithDistance)->not()->toBeNull();
});

it('toSpatialExpressionString can handle a Expression input', function (): void {
    $model = new TestPlace();
    $method = (new ReflectionClass(TestPlace::class))->getMethod('toSpatialExpressionString');

    $result = $method->invoke($model, $model->newQuery(), DB::raw('POINT(longitude, latitude)'));

    expect($result)->toBe('POINT(longitude, latitude)');
});

it('toSpatialExpressionString can handle a Geometry input', function (): void {
    $model = new TestPlace();
    $method = (new ReflectionClass(TestPlace::class))->getMethod('toSpatialExpressionString');
    $polygon = Polygon::fromJson('{"type":"Polygon","coordinates":[[[-1,-1],[1,-1],[1,1],[-1,1],[-1,-1]]]}');

    $result = $method->invoke($model, $model->newQuery(), $polygon);

    $connection = $model->newQuery()->getConnection();
    $sqlSerializedPolygon = $polygon->toSqlExpression($connection)->getValue();
    expect($result)->toBe($sqlSerializedPolygon);
});

it('toSpatialExpressionString can handle a string input', function (): void {
    $model = new TestPlace();
    $method = (new ReflectionClass(TestPlace::class))->getMethod('toSpatialExpressionString');

    $result = $method->invoke($model, $model->newQuery(), 'test_places.point');

    expect($result)->toBe('`test_places`.`point`');
});
