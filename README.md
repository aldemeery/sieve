# Sieve - Clean & Easy Eloquent Filtration

<p>
<a href="https://github.com/aldemeery/sieve/actions"><img src="https://github.com/aldemeery/sieve/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/aldemeery/sieve"><img src="https://img.shields.io/packagist/dt/aldemeery/sieve?label=Downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/aldemeery/sieve"><img src="https://img.shields.io/packagist/v/aldemeery/sieve?label=Latest+Version"" alt="Latest Version"></a>
<a href="https://github.com/aldemeery/sieve/blob/master/LICENSE"><img src="https://img.shields.io/packagist/l/aldemeery/sieve?label=License"" alt="License"></a>
</p>

* [Installation](#installation)
* [Usage](#usage)
  * [Creating Filters](#creating-filters)
  * [Filtering](#filtering)
  * [Mapping Values](#mapping-values)

A minimalist, ultra-lightweight package for clean, intuitive query filtering.

With Sieve, your filtration logic is simplified from something like this:

```php
public function index(Request $request)
{
    $query = Product::query();

    if ($request->has('color')) {
        $query->where('color', $request->get('color'));
    }

    if ($request->has('condition')) {
        $query->where('condition', $request->get('condition'));
    }

    if ($request->has('price')) {
        $direction = $request->get('price') === 'highest' ? 'desc' : 'asc';
        $query->orderBy('price', $direction);
    }

    return $query->get();
}
```

to this:

```php
public function index(Request $request)
{
    return Product::filter($request->query())->get();
}
```

---

## Installation

> [!IMPORTANT]
> This package requires Laravel 11.0 or higher and PHP 8.2 or higher.

You can install the package via composer:

```bash
composer require aldemeery/sieve
```

---

## Usage

Enabling filtration for a model is as easy as adding the `Aldemeery\Sieve\Concerns\Filterable` trait to it:

```php
use Aldemeery\Sieve\Concerns\Filterable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Filterable;

    // ...
}
```
The `Filterable` trait introduces a `filter` [local scope](https://laravel.com/docs/eloquent#local-scopes) to your model, which accepts an associative array for filtration:

```php
public function index(Request $request)
{
    return Product::filter($request->query())->get();
}
```

Now you're ready to create your filter classes.

---

### Creating filters

To create a filter, create a class that implements the `Aldemeery\Sieve\Contracts\Filter` interface.

You can either create a filter class using the `make:filter` artisan command, which will place the filter in the `app/Http/Filters` directory.
Alternatively, you can create a filter class manually and place it wherever you prefer:

```bash
php artisan make:filter Product/ColorFilter
```

This generates a `ColorFilter` class in the `app/Filters/Product` directory:

```php
<?php

namespace App\Filters\Product;

use Aldemeery\Sieve\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/** @implements Filter<\App\Models\Product> */
class ColorFilter implements Filter
{
    public function map(mixed $value): mixed
    {
        return match ($value) {
            default => $value,
        };
    }

    public function apply(Builder $query, mixed $value): void
    {
        // $query->where('id', $value);
    }
}
```

Here, `apply` defines the filtration logic, while `map` can transform input values if needed before passing them to `apply`

> [!IMPORTANT]
> Before a value is passed to the `apply` method, it's first passed to the `map` method.
>
> If you do not need to map values into other values, you should just leave the `map` method as it is.

Check out this examples:

```php
public function map(mixed $value): mixed
{
    return match ($value) {
        'yes' => true,
        'no' => false,
        '1' => true,
        '0' => true,
        default => $value,
    };
}

public function apply(Builder $query, mixed $value): void
{
    // Assuming filter was called like this: Product::filter(['in_stock' => 'yes'])->get();
    // Or like this: Product::filter(['in_stock' => '1'])->get();
    // In both cases, $value would be `true`

    $query->where('in_stock', $value);
}
```

With an instance of `Illuminate\Database\Eloquent\Builder` passed to `apply`, you gain access to its full capabilities, allowing you to perform a wide range of operations:

#### Example 1 - Ordering:

```php
public function apply(Builder $query, mixed $value): void
{
    $query->orderBy('price', $value);
}
```

#### Example 2 - Relations:

```php
public function apply(Builder $query, mixed $value): void
{
    $query->whereHas('category', function($query) use ($value): void {
        $query->where('name', $value);
    });
}
```

---

### Filtering

Once you have created your filters and defined your filtration logic, It's time now to actually use the filter, which can be done in two ways:
- [Passing a filters array](#passing-a-filters-array) as a second parameter to the `filter` scope.
- [Defining model filters](#defining-model-filters) inside the model itself.

#### Passing a filters array:

Use this when you want to apply a filter to a single query:

```php
public function index(Request $request)
{
    return Product::filter($request->query(), [
        // "color" here is the key to be used in the query string
        // e.g. https://example.com/products?color=red
        "color" => \App\Filters\Product\ColorFilter::class,
    ])->get();
}
```

In the above example, the `ColorFilter` is applied *only* for this query.

#### Defining model filters:
Alternatively, if you want a filter to be associated with a model and applied every time the filter method is called, you can add a `filters` method to your model that returns an array mapping keys to their corresponding filter classes:

```php
<?php

namespace App\Models;

use Aldemeery\Sieve\Concerns\Filterable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Filterable;

    /** @return array<string, string> */
    private function filters(): array
    {
        return [
            'color' => \App\Filters\Product\ColorFilter::class,
        ];
    }
}
```

Now everytime you call the `filter` method on the model, you will have the `ColorFilter` applied to your query:

```php
public function index(Request $request)
{
    // The `ColorFilter` filter is applied.
    return Product::filter($request->query())->get();
}
```

> [!IMPORTANT]
> Only filters with keys present in the data array will be applied. Any filters not included in the array will be ignored.
>
> For instance, if your filter array includes only the `color` key, only the corresponding `ColorFilter` will be executed, while any other filters will have no effect on the query.

---

### Mapping Values
In some cases, you may want to use more user-friendly values that do not directly correspond to the values needed for filtration.
This is where the map method comes in handy.

Before any value reaches the `apply` method, it is first processed by the map method.
This allows you to transform incoming values into something more meaningful for your application.

#### Example:
Imagine you want to sort products by price but using the query string, but you prefer using labels like `..?price=lowest` or `..?price=highest` instead of technical terms like `..?price=asc` or `..?price=desc`.

You can achieve this by using the `map` method, as shown below:

```php
<?php

namespace App\Filters\Product;

use Aldemeery\Sieve\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/** @implements Filter<\App\Models\Product> */
class PriceFilter implements Filter
{
    public function map(mixed $value): mixed
    {
        return match ($value) {
            'lowest' => 'asc',
            'highest' => 'desc',
            default => $value,
        };
    }

    public function apply(Builder $query, mixed $value): void
    {
        // After mapping, $value will be 'asc' for 'lowest' and 'desc' for 'highest'.
        $query->orderBy('price', $value);
    }
}
```

With this implementation, you can present a more intuitive interface to users while maintaining the necessary functionality for sorting in your queries.
