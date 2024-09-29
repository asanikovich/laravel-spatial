<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Eloquent;

use ASanikovich\LaravelSpatial\Exceptions\LaravelSpatialException;
use ASanikovich\LaravelSpatial\Geometry\Geometry;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;

class GeometryCast implements CastsAttributes
{
    /** @var class-string<Geometry> */
    private string $className;

    /**
     * @param  class-string<Geometry>  $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @param  string|Expression|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Geometry
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof Expression) {
            $wkt = $this->extractWktFromExpression($value, $model->getConnection());
            $srid = $this->extractSridFromExpression($value, $model->getConnection());

            return $this->className::fromWkt($wkt, $srid);
        }

        return $this->className::fromWkb($value);
    }

    /**
     * @param  Geometry|mixed|null  $value
     * @param  array<string, mixed>  $attributes
     *
     * @throws LaravelSpatialException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?Expression
    {
        if (! $value) {
            return null;
        }

        if (is_array($value)) {
            $value = Geometry::fromArray($value);
        }

        if ($value instanceof Expression) {
            return $value;
        }

        if (! ($value instanceof $this->className)) {
            $geometryType = is_object($value) ? $value::class : gettype($value);

            throw new LaravelSpatialException(sprintf('Expected %s, %s given.', static::class, $geometryType));
        }

        return $value->toSqlExpression($model->getConnection());
    }

    private function extractWktFromExpression(Expression $expression, Connection $connection): string
    {
        $expressionValue = $expression->getValue($connection->getQueryGrammar());

        preg_match('/ST_GeomFromText\(\'(.+)\', .+(, .+)?\)/', (string) $expressionValue, $match);

        return $match[1];
    }

    private function extractSridFromExpression(Expression $expression, Connection $connection): int
    {
        $expressionValue = $expression->getValue($connection->getQueryGrammar());

        preg_match('/ST_GeomFromText\(\'.+\', (.+)(, .+)?\)/', (string) $expressionValue, $match);

        return (int) $match[1];
    }
}
