<?php

namespace ASanikovich\LaravelSpatial\Tests\Database\TestModels;

use ASanikovich\LaravelSpatial\Eloquent\HasSpatial;
use ASanikovich\LaravelSpatial\Geometry\GeometryCollection;
use ASanikovich\LaravelSpatial\Geometry\LineString;
use ASanikovich\LaravelSpatial\Geometry\MultiLineString;
use ASanikovich\LaravelSpatial\Geometry\MultiPoint;
use ASanikovich\LaravelSpatial\Geometry\MultiPolygon;
use ASanikovich\LaravelSpatial\Geometry\Point;
use ASanikovich\LaravelSpatial\Geometry\Polygon;
use ASanikovich\LaravelSpatial\Tests\Database\TestFactories\TestPlaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read int $id_new
 * @property string $name
 * @property Point $point
 * @property MultiPoint $multi_point
 * @property LineString $line_string
 * @property MultiLineString $multi_line_string
 * @property Polygon $polygon
 * @property MultiPolygon $multi_polygon
 * @property GeometryCollection $geometry_collection
 * @property float|null $distance
 * @property float|null $distance_in_meters
 *
 * @mixin Model
 */
class TestPlace extends Model
{
    use HasFactory, HasSpatial;

    protected $fillable = [
        'address',
        'point',
        'multi_point',
        'line_string',
        'multi_line_string',
        'polygon',
        'multi_polygon',
        'geometry_collection',
        'point_with_line_string_cast',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'point' => Point::class,
        'multi_point' => MultiPoint::class,
        'line_string' => LineString::class,
        'multi_line_string' => MultiLineString::class,
        'polygon' => Polygon::class,
        'multi_polygon' => MultiPolygon::class,
        'geometry_collection' => GeometryCollection::class,
        'point_with_line_string_cast' => LineString::class,
    ];

    protected static function newFactory(): TestPlaceFactory
    {
        return new TestPlaceFactory;
    }
}
