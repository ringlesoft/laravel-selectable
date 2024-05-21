# Laravel Selectable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ringlesoft/laravel-selectable.svg?style=flat-square)](https://packagist.org/packages/ringlesoft/laravel-selectable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ringlesoft/laravel-selectable/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/ringlesoft/laravel-selectable/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ringlesoft/laravel-selectable/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/ringlesoft/laravel-selectable/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/ringlesoft/laravel-selectable.svg?style=flat-square)](https://packagist.org/packages/ringlesoft/laravel-selectable)
<!--delete-->
---
A laravel package for generating HTML select options from laravel collections. You do not need to add any trait or extend any class.

## Installation

You can install the package via composer:

```bash
composer require ringlesoft/laravel-selectable
```

## Usage

### 1. Basic Usage
```html
<select name="user_id">
    {!! \\App\\Model\\User::all()->toSelectOptions(); !!}}
</select>
```
The output for this code will be as follows

```bladehtml
<select name="user_id">
    <option value="{{$user->id}}">{{$user->name}}</option>
    ...
</select>
```

### 2. Custom label and value
```bladehtml
<select name="user_id">
    {!! \\App\\Model\\User::all()->toSelectOptions('email', 'uuid'); !!}}
</select>
```
The output for this code will be as follows

```bladehtml
<select name="user_id">
    <option value="{{$user->uuid}}">{{$user->email}}</option>
    ...
</select>
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
        ->withDataAttribute('hidden', fn($item) => $item->status !== 'active')
        ->withClass('form-option custom')
        ->toSelectOptions();
        ->toSelectOptions();
    !!}
</select>
```
#### Available methods
- `withLabel(string|callable $label)`: This method allows you to customize the label for each option. A string will be used as the collection field from which the label will be generated, while a callable will be used to generate the label.
- `withValue(string|callable $value)`: This method allows you to customize the value for each option. A string will be used as the collection field from which the value will be generated, while a callable will be used to generate the value.
- `withSelected(mixed|callable $selected)`: This method allows you to customize the selected options. Can be a `string`, `int`, an array of `string`/`int`, a `model` or a callable that returns a boolean value.
- `withDisabled(mixed|callable $disabled)`: This method allows you to customize the disabled options. Can be a `string`, `int`, an array of `string`/`int`, a `model` or a callable that returns a boolean value.
- `withDataAttribute(string $attribute, mixed|callable $value)`: This method allows you to add a data attribute to each option.
- `withClass(string $class)`: This method allows you to add a class to each option.
- `toSelectItems()`: This method converts the selectable collection to an array of selectable items. Useful for Ajax responses or SPA.
- `toSelectOptions()`: This method converts the selectable collection to an HTML select options string.
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
