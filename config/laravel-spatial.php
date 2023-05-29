<?php

use ASanikovich\LaravelSpatial\Enums\GeometryType;
use ASanikovich\LaravelSpatial\Geometry;

return [
    GeometryType::GEOMETRY_COLLECTION->value => Geometry\GeometryCollection::class,
    GeometryType::LINESTRING->value => Geometry\LineString::class,
    GeometryType::MULTILINESTRING->value => Geometry\MultiLineString::class,
    GeometryType::MULTIPOINT->value => Geometry\MultiPoint::class,
    GeometryType::MULTIPOLYGON->value => Geometry\MultiPolygon::class,
    GeometryType::POINT->value => Geometry\Point::class,
    GeometryType::POLYGON->value => Geometry\Polygon::class,
];
