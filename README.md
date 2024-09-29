# Laravel Spatial

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asanikovich/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/asanikovich/laravel-spatial)
[![GitHub Tests Status](https://img.shields.io/github/actions/workflow/status/asanikovich/laravel-spatial/pest.yml?branch=master&label=tests&style=flat-square)](https://github.com/asanikovich/laravel-spatial/actions/workflows/pest.yml?query=branch%3Amaster)
[![GitHub Tests Coverage Status](https://img.shields.io/codecov/c/github/asanikovich/laravel-spatial?token=E0703O0PPT&style=flat-square)](https://github.com/asanikovich/laravel-spatial/actions/workflows/pest-coverage.yml?query=branch%3Amaster)
[![GitHub Code Style Status](https://img.shields.io/github/actions/workflow/status/asanikovich/laravel-spatial/phpstan.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/asanikovich/laravel-spatial/actions/workflows/phpstan.yml?query=branch%3Amaster)
[![GitHub Lint Status](https://img.shields.io/github/actions/workflow/status/asanikovich/laravel-spatial/pint.yml?branch=master&label=lint&style=flat-square)](https://github.com/asanikovich/laravel-spatial/actions/workflows/pint.yml?query=branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/asanikovich/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/asanikovich/laravel-spatial)
[![Licence](https://img.shields.io/packagist/l/asanikovich/laravel-spatial.svg?style=flat-square)](https://packagist.org/packages/asanikovich/laravel-spatial)

**This Laravel package allows you to easily work with spatial data types and functions.**

* v2 supports Laravel 10,11 and PHP 8.1+
* v1 supports Laravel 8,9 and PHP 8.1+

This package supports MySQL v8 or v5.7, and MariaDB v10.

## Getting Started

### Installing the Package

You can install the package via composer:

```bash
composer require asanikovich/laravel-spatial
```

### Configuration

Default Configuration file includes geometry types mapping:
```php
<?php

use ASanikovich\LaravelSpatial\Enums\GeometryType;
use ASanikovich\LaravelSpatial\Geometry;

return [
    GeometryType::POINT->value => Geometry\Point::class,
    GeometryType::POLYGON->value => Geometry\Polygon::class,
    /// ...
];
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-spatial-config"
```

If you want you can override custom geometry types mapping:
* globally by config file
* by custom `$casts` in your model (top priority)

### Setting Up Your First Model

1. First, generate a new model along with a migration file by running:

   ```bash
   php artisan make:model {modelName} --migration
   ```

2. Next, add some spatial columns to the migration file. For instance, to create a "places" table:

    ```php
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;

    class CreatePlacesTable extends Migration
    {
        public function up(): void
        {
            Schema::create('places', static function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->point('location')->nullable();
                $table->polygon('area')->nullable();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('places');
        }
    }
    ```

3. Run the migration:

    ```bash
    php artisan migrate
    ```

4. In your new model, fill `$casts` arrays and use the `HasSpatial` trait (fill the `$fillable` - optional):

    ```php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use ASanikovich\LaravelSpatial\Eloquent\HasSpatial;
    use ASanikovich\LaravelSpatial\Geometry\Point;
    use ASanikovich\LaravelSpatial\Geometry\Polygon;

    /**
     * @property Point $location
     * @property Polygon $area
     */
    class Place extends Model
    {
        use HasSpatial;

        protected $fillable = [
            'name',
            'location',
            'area',
        ];

        protected $casts = [
            'location' => Point::class,
            'area' => Polygon::class,
        ];
    }
    ```

### Interacting with Spatial Data

After setting up your model, you can now create and access spatial data. Here's an example:

```php
use App\Models\Place;
use ASanikovich\LaravelSpatial\Geometry\Polygon;
use ASanikovich\LaravelSpatial\Geometry\LineString;
use ASanikovich\LaravelSpatial\Geometry\Point;
use ASanikovich\LaravelSpatial\Enums\Srid;

// Create new records

$londonEye = Place::create([
    'name' => 'London Eye',
    'location' => new Point(51.5032973, -0.1217424),
]);

$whiteHouse = Place::create([
    'name' => 'White House',
    'location' => new Point(38.8976763, -77.0365298, Srid::WGS84->value), // with SRID
]);

$vaticanCity = Place::create([
    'name' => 'Vatican City',
    'area' => new Polygon([
        new LineString([
              new Point(12.455363273620605, 41.90746728266806),
              new Point(12.450309991836548, 41.906636872349075),
              new Point(12.445632219314575, 41.90197359839437),
              new Point(12.447413206100464, 41.90027269624499),
              new Point(12.457906007766724, 41.90000118654431),
              new Point(12.458517551422117, 41.90281205461268),
              new Point(12.457584142684937, 41.903107507989986),
              new Point(12.457734346389769, 41.905918239316286),
              new Point(12.45572805404663, 41.90637337450963),
              new Point(12.455363273620605, 41.90746728266806),
        ]),
    ]),
])

// Access the data

echo $londonEye->location->latitude; // 51.5032973
echo $londonEye->location->longitude; // -0.1217424

echo $whiteHouse->location->srid; // 4326

echo $vacationCity->area->toJson(); // {"type":"Polygon","coordinates":[[[41.90746728266806,12.455363273620605],[41.906636872349075,12.450309991836548],[41.90197359839437,12.445632219314575],[41.90027269624499,12.447413206100464],[41.90000118654431,12.457906007766724],[41.90281205461268,12.458517551422117],[41.903107507989986,12.457584142684937],[41.905918239316286,12.457734346389769],[41.90637337450963,12.45572805404663],[41.90746728266806,12.455363273620605]]]}
```

## Further Reading

For more comprehensive documentation on the API, please refer to the [API](API.md) page.

Create queries only with scopes methods:
```php
Place::whereDistance(...); // This is IDE-friendly
```

## Extension

You can add new methods to the `Geometry` class through macros.

Here's an example of how to register a macro in your service provider's `boot` method:

```php
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Geometry::macro('getName', function (): string {
            /** @var Geometry $this */
            return class_basename($this);
        });
    }
}
```

Use the method in your code:

```php
$londonEyePoint = new Point(51.5032973, -0.1217424);

echo $londonEyePoint->getName(); // Point
```

## Development
Here are some useful commands for development

Before running tests run db by docker-compose:
```bash
docker-compose up -d
```
Run tests:
```bash
composer run test
```
Run tests with coverage:
```bash
composer run test-coverage
```
Perform type checking:
```bash
composer run phpstan
```
Format your code:
```bash
composer run format
```

## Updates and Changes

For details on updates and changes, please refer to our [CHANGELOG](CHANGELOG.md).

## License

Laravel Spatial is released under The MIT License (MIT). For more information, please see our [License File](LICENSE.md).

## Credits

Originally inspired from [MatanYadaev's laravel-eloquent-spatial package](https://github.com/MatanYadaev/laravel-eloquent-spatial).
