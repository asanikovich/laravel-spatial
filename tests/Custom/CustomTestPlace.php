<?php

namespace ASanikovich\LaravelSpatial\Tests\Custom;

use ASanikovich\LaravelSpatial\Tests\Database\TestFactories\TestPlaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property CustomPoint $point
 *
 * @mixin Model
 */
class CustomTestPlace extends Model
{
    use HasFactory;

    protected $table = 'test_places';

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

    protected $casts = [
        'point' => CustomPoint::class,
    ];

    protected static function newFactory(): TestPlaceFactory
    {
        return new TestPlaceFactory;
    }
}
