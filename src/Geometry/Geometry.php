<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Geometry;

use ASanikovich\LaravelSpatial\Database\Connection;
use ASanikovich\LaravelSpatial\Eloquent\GeometryCast;
use ASanikovich\LaravelSpatial\Exceptions\LaravelSpatialException;
use ASanikovich\LaravelSpatial\Exceptions\LaravelSpatialJsonException;
use geoPHP;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Macroable;
use JsonException;
use JsonSerializable;
use Stringable;
use Throwable;
use WKB as geoPHPWkb;

abstract class Geometry implements Castable, Arrayable, Jsonable, JsonSerializable, Stringable
{
    use Macroable;

    public int $srid = 0;

    abstract public function toWkt(): string;

    abstract public function getWktData(): string;

    public function __toString(): string
    {
        return $this->toWkt();
    }

    /**
     * @throws LaravelSpatialJsonException
     */
    public function toJson($options = 0): string
    {
        try {
            return json_encode($this, $options | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) { // @codeCoverageIgnore
            throw new LaravelSpatialJsonException($e->getMessage(), previous: $e); // @codeCoverageIgnore
        } // @codeCoverageIgnore
    }

    /**
     * @throws LaravelSpatialException
     */
    public function toWkb(): string
    {
        try {
            $geoPHPGeometry = geoPHP::load($this->toJson());
        } catch (Throwable $e) { // @codeCoverageIgnore
            throw new LaravelSpatialException($e->getMessage(), previous: $e); // @codeCoverageIgnore
        } // @codeCoverageIgnore

        $sridInBinary = pack('L', $this->srid);

        // @phpstan-ignore-next-line
        $wkbWithoutSrid = (new geoPHPWkb)->write($geoPHPGeometry);

        return $sridInBinary.$wkbWithoutSrid;
    }

    public static function fromWkb(string $wkb): Geometry
    {
        $srid = substr($wkb, 0, 4);
        // @phpstan-ignore-next-line
        $srid = unpack('L', $srid)[1];

        $wkb = substr($wkb, 4);

        $geometry = GeometryFactory::parse($wkb, static::class);
        $geometry->srid = $srid;

        if (! ($geometry instanceof static)) {
            throw new LaravelSpatialException(sprintf('Expected %s, %s given.', static::class, $geometry::class));
        }

        return $geometry;
    }

    public static function fromWkt(string $wkt, int $srid = 0): static
    {
        $geometry = GeometryFactory::parse($wkt, static::class);
        $geometry->srid = $srid;

        if (! ($geometry instanceof static)) {
            throw new LaravelSpatialException(sprintf('Expected %s, %s given.', static::class, $geometry::class));
        }

        return $geometry;
    }

    public static function fromJson(string $geoJson, int $srid = 0): static
    {
        $geometry = GeometryFactory::parse($geoJson, static::class);
        $geometry->srid = $srid;

        if (! ($geometry instanceof static)) {
            throw new LaravelSpatialException(sprintf('Expected %s, %s given.', static::class, $geometry::class));
        }

        return $geometry;
    }

    /**
     * @param  array<mixed>  $geometry
     *
     * @throws LaravelSpatialJsonException
     */
    public static function fromArray(array $geometry): static
    {
        try {
            $geoJson = json_encode($geometry, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) { // @codeCoverageIgnore
            throw new LaravelSpatialJsonException($e->getMessage(), previous: $e); // @codeCoverageIgnore
        } // @codeCoverageIgnore

        return static::fromJson($geoJson);
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array{type: string, coordinates: array<mixed>}
     */
    public function toArray(): array
    {
        return [
            'type' => class_basename(static::class),
            'coordinates' => $this->getCoordinates(),
        ];
    }

    /**
     * @throws JsonException
     */
    public function toFeatureCollectionJson(): string
    {
        if (static::class === GeometryCollection::class) {
            /** @var GeometryCollection $this */
            $geometries = $this->geometries;
        } else {
            $geometries = collect([$this]);
        }

        $features = $geometries->map(static function (self $geometry): array {
            return [
                'type' => 'Feature',
                'properties' => [],
                'geometry' => $geometry->toArray(),
            ];
        });

        return json_encode([
            'type' => 'FeatureCollection',
            'features' => $features,
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<mixed>
     */
    abstract public function getCoordinates(): array;

    /**
     * @param  array<string>  $arguments
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new GeometryCast(static::class);
    }

    public function toSqlExpression(ConnectionInterface $connection): Expression
    {
        $wkt = $this->toWkt();

        if (! (new Connection())->isSupportAxisOrder($connection)) {
            // @codeCoverageIgnoreStart
            return DB::raw(sprintf("ST_GeomFromText('%s', %d)", $wkt, $this->srid));
            // @codeCoverageIgnoreEnd
        }

        return DB::raw(sprintf("ST_GeomFromText('%s', %d, 'axis-order=long-lat')", $wkt, $this->srid));
    }
}
