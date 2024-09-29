<?php

declare(strict_types=1);

namespace ASanikovich\LaravelSpatial;

use ASanikovich\LaravelSpatial\Enums\GeometryType;
use ASanikovich\LaravelSpatial\Exceptions\LaravelSpatialException;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\DatabaseServiceProvider;
use RuntimeException;
use Throwable;

final class LaravelSpatialServiceProvider extends DatabaseServiceProvider
{
    /**
     * @throws LaravelSpatialException
     * @throws Throwable
     */
    public function boot(): void
    {
        $this->publishes(
            [__DIR__.'/../config/laravel-spatial.php' => config_path('laravel-spatial.php')],
            'laravel-spatial-config'
        );

        $this->mergeConfigFrom(__DIR__.'/../config/laravel-spatial.php', 'laravel-spatial');

        $this->validateConfig();

        $this->registerDoctrineTypes();
    }

    /**
     * @throws Throwable
     */
    private function registerDoctrineTypes(): void
    {
        foreach (GeometryType::cases() as $type) {
            $this->registerDoctrineType($type->getDoctrineClassName(), $type->value);
        }

        $this->registerDoctrineType(GeometryType::GEOMETRY_COLLECTION->getDoctrineClassName(), 'geomcollection');
    }

    /**
     * @throws LaravelSpatialException
     */
    private function validateConfig(): void
    {
        /** @var array<class-string<Geometry\Geometry>>|array<string> $config */
        $config = config('laravel-spatial');

        foreach (GeometryType::cases() as $type) {
            $configType = $config[$type->value] ?? null;
            if (! $configType) {
                throw new LaravelSpatialException(
                    sprintf('Invalid class for geometry type "%s", please check config', $type->value)
                );
            }

            $baseClass = $type->getBaseGeometryClassName();
            /** @phpstan-ignore-next-line  */
            if ($configType !== $baseClass && ! $configType instanceof $baseClass) {
                throw new LaravelSpatialException(sprintf(
                    'Class for geometry type "%s" should be instance of "%s" ("%s" provided), please check config',
                    $type->value,
                    $baseClass,
                    $configType,
                ));
            }
        }
    }

    private function isDoctrineAvailable(): bool
    {
        return class_exists('Doctrine\DBAL\Connection');
    }

    private function registerDoctrineType(Type|string $class, string $name): void
    {
        if (! $this->isDoctrineAvailable()) {
            throw new RuntimeException(
                'Registering a custom Doctrine type requires Doctrine DBAL (doctrine/dbal).'
            );
        }

        if (! Type::hasType($name)) {
            /** @var Type $type */
            $type = is_string($class) ? new $class : $class;
            Type::getTypeRegistry()->register($name, $type);
        }
    }
}
