# Laravel Repository System

**LARS** abstract the data layer, making it easy to maintain.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/omatech/lars.svg?style=flat-square)](https://packagist.org/packages/omatech/lars)
[![Total Downloads](https://img.shields.io/packagist/dt/omatech/lars.svg?style=flat-square)](https://packagist.org/packages/omatech/lars)

## Installation

You can install the package via composer:

```bash
composer require omatech/lars
```

## Usage

Create your own repository class extending BaseRepository class, use the model function to set the eloquent model for the repository.

``` php
class Repository extends BaseRepository
{
    public function model() : String
    {
        return Model::class;
    }
}
```

Now you can create your own function using query builder easily.

``` php
public function method()
{
    return $this->query()
                ->get();
}
```

### Criterias

Create a criteria implementing the interface CriteriaInterface.
With the apply function you can filter by your own criteria.

``` php
public class Criteria implements CriteriaInterface
{
    public function apply(Builder $q) : Builder
    {
        return $q->where('role', 'admin');
    }
}
```

#### Use your criteria

``` php
public function method()
{
    return $this->pushCriteria(new FooCriteria)
                ->query()
                ->get();
}
```

#### List applied criterias

``` php
public function method()
{
    return $this->getCriterias();
}
```

#### Remove an applied criteria

``` php
public function method()
{
    return $this->pushCriteria(new FooCriteria)
                ->popCriteria(new FooCriteria)
                ->query()
                ->get();
}
```

#### Remove all applied criterias

``` php
public function method()
{
    return $this->pushCriteria(new FooCriteria)
                ->pushCriteria(new BarCriteria)
                ->resetCriteria()
                ->query()
                ->get();
}
```

#### Global criterias for all methods in repository

It is possible to apply a criteria to all methods using the construct method of the repository.

``` php
public function __construct()
{
    $this->pushCriteria(new FooCriteria);
}
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email cbohollo@omatech.com instead of using the issue tracker.

## Credits

- [Christian Bohollo](https://github.com/omatech)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.