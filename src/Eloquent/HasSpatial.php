<?php

namespace ASanikovich\LaravelSpatial\Eloquent;

use ASanikovich\LaravelSpatial\Geometry\Geometry;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static withDistance(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn, string $alias = 'distance')
 * @method static withDistanceSphere(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn, string $alias = 'distance')
 * @method static whereDistance(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn, string $operator, int|float $value)
 * @method static whereDistanceSphere(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn, string $operator, int|float $value)
 * @method static whereWithin(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereNotWithin(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereContains(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereNotContains(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereTouches(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereIntersects(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereCrosses(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereDisjoint(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereOverlaps(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereEquals(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn)
 * @method static whereSrid(Expression|Geometry|string $column, string $operator, int|float $value)
 * @method static orderByDistanceSphere(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn, string $direction = 'asc')
 * @method static orderByDistance(Expression|Geometry|string $column, Expression|Geometry|string $geometryOrColumn, string $direction = 'asc')
 */
trait HasSpatial
{
    public function scopeWithDistance(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
        string $alias = 'distance'
    ): Builder {
        if (empty($query->getQuery()->columns)) {
            $query->select();
        }

        return $query->selectRaw(sprintf(
            'ST_DISTANCE(%s, %s) AS %s',
            $this->toSpatialExpressionString($query, $column),
            $this->toSpatialExpressionString($query, $geometryOrColumn),
            $alias,
        ));
    }

    public function scopeWithDistanceSphere(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
        string $alias = 'distance'
    ): Builder {
        if (empty($query->getQuery()->columns)) {
            $query->select();
        }

        return $query->selectRaw(sprintf(
            'ST_DISTANCE_SPHERE(%s, %s) AS %s',
            $this->toSpatialExpressionString($query, $column),
            $this->toSpatialExpressionString($query, $geometryOrColumn),
            $alias,
        ));
    }

    public function scopeWhereDistance(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
        string $operator,
        int|float $value
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_DISTANCE(%s, %s) %s ?',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
                $operator,
            ),
            [$value],
        );

        return $query;
    }

    public function scopeOrderByDistance(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
        string $direction = 'asc'
    ): Builder {
        $query->orderByRaw(
            sprintf(
                'ST_DISTANCE(%s, %s) %s',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
                $direction,
            )
        );

        return $query;
    }

    public function scopeWhereDistanceSphere(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
        string $operator,
        int|float $value
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_DISTANCE_SPHERE(%s, %s) %s ?',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
                $operator,
            ),
            [$value],
        );

        return $query;
    }

    public function scopeOrderByDistanceSphere(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
        string $direction = 'asc'
    ): Builder {
        $query->orderByRaw(
            sprintf(
                'ST_DISTANCE_SPHERE(%s, %s) %s',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
                $direction
            )
        );

        return $query;
    }

    public function scopeWhereWithin(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_WITHIN(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereNotWithin(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_WITHIN(%s, %s) = 0',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereContains(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_CONTAINS(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereNotContains(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_CONTAINS(%s, %s) = 0',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereTouches(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_TOUCHES(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereIntersects(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_INTERSECTS(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereCrosses(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_CROSSES(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereDisjoint(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_DISJOINT(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereOverlaps(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_OVERLAPS(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereEquals(
        Builder $query,
        Expression|Geometry|string $column,
        Expression|Geometry|string $geometryOrColumn,
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_EQUALS(%s, %s)',
                $this->toSpatialExpressionString($query, $column),
                $this->toSpatialExpressionString($query, $geometryOrColumn),
            )
        );

        return $query;
    }

    public function scopeWhereSrid(
        Builder $query,
        Expression|Geometry|string $column,
        string $operator,
        int|float $value
    ): Builder {
        $query->whereRaw(
            sprintf(
                'ST_SRID(%s) %s ?',
                $this->toSpatialExpressionString($query, $column),
                $operator,
            ),
            [$value],
        );

        return $query;
    }

    protected function toSpatialExpressionString(Builder $query, Expression|Geometry|string $value): string
    {
        $grammar = $query->getGrammar();

        if ($value instanceof Expression) {
            $expression = $value;
        } elseif ($value instanceof Geometry) {
            $expression = $value->toSqlExpression($query->getConnection());
        } else {
            $expression = $query->raw($grammar->wrap($value));
        }

        return (string) $expression->getValue($grammar);
    }
}
