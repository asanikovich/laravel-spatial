<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Geometry;

class Point extends Geometry
{
    public function __construct(readonly float $latitude, readonly float $longitude, int $srid = 0)
    {
        $this->srid = $srid;
    }

    public function toWkt(): string
    {
        $wktData = $this->getWktData();

        return sprintf('POINT(%s)', $wktData);
    }

    public function getWktData(): string
    {
        return sprintf('%s %s', $this->longitude, $this->latitude);
    }

    /**
     * @return array{0: float, 1: float}
     */
    public function getCoordinates(): array
    {
        return [
            $this->longitude,
            $this->latitude,
        ];
    }
}
