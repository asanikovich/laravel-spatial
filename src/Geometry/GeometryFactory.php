<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Geometry;

use Geometry as geoPHPGeometry;
use GeometryCollection as geoPHPGeometryCollection;
use geoPHP;
use LineString as geoPHPLineString;
use ASanikovich\LaravelSpatial\Enums\GeometryType;
use ASanikovich\LaravelSpatial\Exceptions\LaravelSpatialException;
use MultiLineString as geoPHPMultiLineString;
use MultiPoint as geoPHPMultiPoint;
use MultiPolygon as geoPHPMultiPolygon;
use Point as geoPHPPoint;
use Polygon as geoPHPPolygon;
use Throwable;

class GeometryFactory
{
    /**
     * @throws LaravelSpatialException
     */
    public static function parse(string $value, string $geometryClass): Geometry
    {
        try {
            /** @var geoPHPGeometry|false $geoPHPGeometry */
            $geoPHPGeometry = geoPHP::load($value);
        } catch (Throwable $e) {
            throw new LaravelSpatialException('Invalid spatial value', previous: $e);
        } finally {
            if (! isset($geoPHPGeometry) || ! $geoPHPGeometry) {
                throw new LaravelSpatialException('Invalid spatial value');
            }
        }

        return self::createFromGeometry($geoPHPGeometry, $geometryClass);
    }

    protected static function createFromGeometry(geoPHPGeometry $geometry, string $geometryClass): Geometry
    {
        $srid = is_int($geometry->getSRID()) ? $geometry->getSRID() : 0;

        if ($geometry instanceof geoPHPPoint) {
            if ($geometry->coords[0] === null || $geometry->coords[1] === null) {
                throw new LaravelSpatialException('Invalid spatial value');
            }

            $class = self::getGeometryClass(GeometryType::POINT, $geometryClass);

            return new $class($geometry->coords[1], $geometry->coords[0], $srid);
        }

        /** @var geoPHPGeometryCollection $geometry */
        $components = collect($geometry->components)
            ->map(static fn (geoPHPGeometry $component) => self::createFromGeometry($component, $geometryClass));

        $type = match ($geometry::class) {
            geoPHPMultiPoint::class => GeometryType::MULTIPOINT,
            geoPHPLineString::class => GeometryType::LINESTRING,
            geoPHPPolygon::class => GeometryType::POLYGON,
            geoPHPMultiLineString::class => GeometryType::MULTILINESTRING,
            geoPHPMultiPolygon::class => GeometryType::MULTIPOLYGON,
            default => GeometryType::GEOMETRY_COLLECTION,
        };

        $class = self::getGeometryClass($type, $geometryClass);

        return new $class($components, $srid);
    }

    /**
     * @param  class-string<Geometry>  $geometryClass
     * @return class-string<Geometry>
     */
    private static function getGeometryClass(GeometryType $type, string $geometryClass): string
    {
        $classFromConfig = config('laravel-spatial.'.$type->value);
        $classFromBase = $type->getBaseGeometryClassName();

        if (is_subclass_of($geometryClass, $classFromBase) || is_subclass_of($geometryClass, $classFromConfig)) {
            return $geometryClass;
        }

        return $classFromConfig;
    }
}
