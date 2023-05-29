<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Geometry;

class LineString extends PointCollection
{
    protected int $minimumGeometries = 2;

    public function toWkt(): string
    {
        $wktData = $this->getWktData();

        return sprintf('LINESTRING(%s)', $wktData);
    }

    public function getWktData(): string
    {
        return $this->geometries
            ->map(static fn (Point $point): string => $point->getWktData())
            ->join(', ');
    }
}
