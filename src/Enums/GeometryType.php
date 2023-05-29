<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Enums;

use Doctrine\DBAL\Types\Type;
use ASanikovich\LaravelSpatial\Doctrine;
use ASanikovich\LaravelSpatial\Geometry;

enum GeometryType: string
{
    case GEOMETRY_COLLECTION = 'geometrycollection';
    case LINESTRING = 'linestring';
    case MULTILINESTRING = 'multilinestring';
    case MULTIPOINT = 'multipoint';
    case MULTIPOLYGON = 'multipolygon';
    case POINT = 'point';
    case POLYGON = 'polygon';

    /**
     * @return class-string<Type>
     */
    public function getDoctrineClassName(): string
    {
        return match ($this) {
            self::GEOMETRY_COLLECTION => Doctrine\GeometryCollectionType::class,
            self::LINESTRING => Doctrine\LineStringType::class,
            self::MULTILINESTRING => Doctrine\MultiLineStringType::class,
            self::MULTIPOINT => Doctrine\MultiPointType::class,
            self::MULTIPOLYGON => Doctrine\MultiPolygonType::class,
            self::POINT => Doctrine\PointType::class,
            self::POLYGON => Doctrine\PolygonType::class,
        };
    }

    /**
     * @return class-string<Geometry\Geometry>
     */
    public function getBaseGeometryClassName(): string
    {
        return match ($this) {
            self::GEOMETRY_COLLECTION => Geometry\GeometryCollection::class,
            self::LINESTRING => Geometry\LineString::class,
            self::MULTILINESTRING => Geometry\MultiLineString::class,
            self::MULTIPOINT => Geometry\MultiPoint::class,
            self::MULTIPOLYGON => Geometry\MultiPolygon::class,
            self::POINT => Geometry\Point::class,
            self::POLYGON => Geometry\Polygon::class,
        };
    }
}
