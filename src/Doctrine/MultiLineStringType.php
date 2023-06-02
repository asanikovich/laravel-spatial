<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial\Doctrine;

use ASanikovich\LaravelSpatial\Enums\GeometryType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class MultiLineStringType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return GeometryType::MULTILINESTRING->value;
    }

    public function getName(): string
    {
        return GeometryType::MULTILINESTRING->value;
    }
}
