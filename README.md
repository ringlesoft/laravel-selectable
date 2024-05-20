# Laravel Selectable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ringlesoft/laravel-selectable.svg?style=flat-square)](https://packagist.org/packages/ringlesoft/laravel-selectable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ringlesoft/laravel-selectable/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ringlesoft/laravel-selectable/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ringlesoft/laravel-selectable/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ringlesoft/laravel-selectable/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ringlesoft/laravel-selectable.svg?style=flat-square)](https://packagist.org/packages/ringlesoft/laravel-selectable)
<!--delete-->
---
A laravel package for generating HTML select options from laravel collections. You do not need to add any trait or extend any class.

## Installation

You can install the package via composer:

```bash
composer require ringlesoft/laravel-selectable
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-selectable-config"
```

This is the contents of the published config file:

```php
return [
    "css_library" => "tailwind",
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-selectable-views"
```

## Usage

### 1. Basic Usage
```html
<select name="user_id" >
    {!! \\App\\Model\\User::all()->toSelectOptions(); !!}}
</select>
```
The output for this code will be as follows

```bladehtml
<option value="{{$user->id}}">{{$user->name}}</option>
...
```

### 2. Custom text and value for options
```bladehtml
<select name="user_id" >
    {!! \\App\\Model\\User::all()->toSelectOptions('email', 'uuid'); !!}}
</select>
```
The output for this code will be as follows

```bladehtml
<option value="{{$user->uuid}}">{{$user->email}}</option>
...
```

### 3. Advanced use
The method `toSelectable` is used to convert the collection into a `Selectable` object. The `Selectable` object has several methods that allow you to customize the options and their properties.

```bladehtml
<select name="user_id" multiple="multiple">
    {!!
        $x = \App\Models\User::all()
        ->toSelectable()
        ->withValue('id')
        ->withLabel(fn($user) => "{$user->first_name} {$user->last_name}")
        ->withSelected([4, 5])
        ->withDisabled(fn($item) => $item->status = 'inactive')
        ->toSelectOptions();
    !!}
</select>
```
### 4. Available methods
- `toSelectable()`: This method returns the current selectable object.
- `withLabel(string|callable $label)`: This method allows you to customize the label for each option. A string will be used as the collection field from which the label will be generated, while a callable will be used to generate the label.
- `withValue(string|callable $value)`: This method allows you to customize the value for each option. A string will be used as the collection field from which the value will be generated, while a callable will be used to generate the value.
- `withSelected(mixed|callable $selected)`: This method allows you to customize the selected options. Can be a `string`, `int`, an array of `string`/`int`, a `model` or a callable that returns a boolean value.
- `withDisabled(mixed|callable $disabled)`: This method allows you to customize the disabled options. Can be a `string`, `int`, an array of `string`/`int`, a `model` or a callable that returns a boolean value.
- `toSelectOptions()`: This method converts the selectable collection to an HTML select options string.
- `toSelectItems()`: This method converts the selectable collection to an array of selectable items. Useful for Ajax responses or SPA.
- 

> <small><strong>Note:</strong> Writing queries within blade templates is not recommended. This is only for simplifying demonstration</small>

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [David Ringle](https://github.com/ringunger)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
