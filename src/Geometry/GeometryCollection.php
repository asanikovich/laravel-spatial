<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Geometry;

use ArrayAccess;
use ASanikovich\LaravelSpatial\Exceptions\LaravelSpatialException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GeometryCollection extends Geometry implements ArrayAccess
{
    /** @var Collection<int, Geometry> */
    protected Collection $geometries;

    protected string $collectionOf = Geometry::class;

    protected int $minimumGeometries = 0;

    /**
     * @param  Collection<int, Geometry>|array<int, Geometry>  $geometries
     *
     * @throws LaravelSpatialException
     */
    public function __construct(Collection|array $geometries, int $srid = 0)
    {
        if (is_array($geometries)) {
            $geometries = collect($geometries);
        }

        $this->geometries = $geometries;
        $this->srid = $srid;

        $this->geometries->each(fn (mixed $geometry) => $this->validateGeometriesType($geometry));
        $this->validateGeometriesCount();
    }

    public function toWkt(): string
    {
        $wktData = $this->getWktData();

        return sprintf('GEOMETRYCOLLECTION(%s)', $wktData);
    }

    public function getWktData(): string
    {
        return $this->geometries
            ->map(static fn (Geometry $geometry): string => $geometry->toWkt())
            ->join(', ');
    }

    /**
     * @return array<mixed>
     */
    public function getCoordinates(): array
    {
        return $this->geometries
            ->map(static fn (Geometry $geometry): array => $geometry->getCoordinates())
            ->all();
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        if ($this->isExtended()) {
            return parent::toArray();
        }

        return [
            'type' => class_basename(static::class),
            'geometries' => $this->geometries->map(static fn (Geometry $geometry): array => $geometry->toArray()),
        ];
    }

    /**
     * @return Collection<int, Geometry>
     */
    public function getGeometries(): Collection
    {
        return new Collection($this->geometries->all());
    }

    /**
     * @param  int  $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->geometries[$offset]);
    }

    /**
     * @param  int  $offset
     */
    public function offsetGet($offset): ?Geometry
    {
        return $this->geometries[$offset];
    }

    /**
     * @param  int  $offset
     * @param  Geometry  $value
     *
     * @throws LaravelSpatialException
     */
    public function offsetSet($offset, $value): void
    {
        $this->validateGeometriesType($value);
        $this->geometries[$offset] = $value;
    }

    /**
     * @param  int  $offset
     */
    public function offsetUnset($offset): void
    {
        $this->geometries->splice($offset, 1);
        $this->validateGeometriesCount();
    }

    /**
     * @throws LaravelSpatialException
     */
    protected function validateGeometriesCount(): void
    {
        $geometriesCount = $this->geometries->count();
        if ($geometriesCount < $this->minimumGeometries) {
            throw new LaravelSpatialException(
                sprintf(
                    '%s must contain at least %s %s',
                    static::class,
                    $this->minimumGeometries,
                    Str::plural('entries', $geometriesCount)
                )
            );
        }
    }

    /**
     * @throws LaravelSpatialException
     */
    protected function validateGeometriesType(mixed $geometry): void
    {
        if (! is_object($geometry) || ! ($geometry instanceof $this->collectionOf)) {
            throw new LaravelSpatialException(
                sprintf('%s must be a collection of %s', static::class, $this->collectionOf)
            );
        }
    }

    private function isExtended(): bool
    {
        return is_subclass_of(static::class, self::class);
    }
}
