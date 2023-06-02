<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Geometry;

class MultiPoint extends PointCollection
{
    protected int $minimumGeometries = 1;

    public function toWkt(): string
    {
        $wktData = $this->getWktData();

        return sprintf('MULTIPOINT(%s)', $wktData);
    }

    public function getWktData(): string
    {
        return $this->geometries
            ->map(static fn (Point $point): string => $point->getWktData())
            ->join(', ');
    }
}
