<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Geometry;

class Polygon extends MultiLineString
{
    public function toWkt(): string
    {
        $wktData = $this->getWktData();

        return sprintf('POLYGON(%s)', $wktData);
    }

    public function getWktData(): string
    {
        return $this->geometries
            ->map(static function (LineString $lineString): string {
                $wktData = $lineString->getWktData();

                return sprintf('(%s)', $wktData);
            })
            ->join(', ');
    }
}
