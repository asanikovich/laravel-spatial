<?php

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;
use ASanikovich\LaravelSpatial\Doctrine\GeometryCollectionType;
use ASanikovich\LaravelSpatial\Doctrine\LineStringType;
use ASanikovich\LaravelSpatial\Doctrine\MultiLineStringType;
use ASanikovich\LaravelSpatial\Doctrine\MultiPointType;
use ASanikovich\LaravelSpatial\Doctrine\MultiPolygonType;
use ASanikovich\LaravelSpatial\Doctrine\PointType;
use ASanikovich\LaravelSpatial\Doctrine\PolygonType;

it('uses custom Doctrine types for spatial columns',
    function (string $column, string $typeClass, string $typeName): void {
        /** @var class-string<Type> $typeClass */
        $doctrineSchemaManager = DB::connection()->getDoctrineSchemaManager();

        $columns = $doctrineSchemaManager->listTableColumns('test_places');
        $dbPlatform = DB::getDoctrineConnection()->getDatabasePlatform();

        expect($columns[$column]->getType())->toBeInstanceOf($typeClass)
            ->and($columns[$column]->getType()->getName())->toBe($typeName)
            ->and($columns[$column]->getType()->getSQLDeclaration([''], $dbPlatform))->toBe($typeName);
    })->with([
        'point' => ['point', PointType::class, 'point'],
        'line_string' => ['line_string', LineStringType::class, 'linestring'],
        'multi_point' => ['multi_point', MultiPointType::class, 'multipoint'],
        'polygon' => ['polygon', PolygonType::class, 'polygon'],
        'multi_line_string' => ['multi_line_string', MultiLineStringType::class, 'multilinestring'],
        'multi_polygon' => ['multi_polygon', MultiPolygonType::class, 'multipolygon'],
        'geometry_collection' => ['geometry_collection', GeometryCollectionType::class, 'geometrycollection'],
    ]);
