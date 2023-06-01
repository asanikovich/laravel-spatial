<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Doctrine;

use ASanikovich\LaravelSpatial\Enums\GeometryType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class MultiPointType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return GeometryType::MULTIPOINT->value;
    }

    public function getName(): string
    {
        return GeometryType::MULTIPOINT->value;
    }
}
