# Laravel Selectable
[![Latest Version on Packagist](https://img.shields.io/packagist/v/ringlesoft/laravel-selectable.svg?style=flat-square)](https://packagist.org/packages/ringlesoft/laravel-selectable)
[![Total Downloads](https://img.shields.io/packagist/dt/ringlesoft/laravel-selectable.svg?style=flat-square)](https://packagist.org/packages/ringlesoft/laravel-selectable)
***
Laravel Selectable is a powerful package that simplifies the process of generating HTML select options from Laravel
collections. With its flexible and intuitive syntax, you can easily create customized select options without the need
for
additional traits or class extensions.

## Features

- Generate select options from Laravel collections with ease
- Customize label and value fields using strings or closures
- Specify selected and disabled options
- Add data attributes and classes to options
- Support for grouping options
- Convert collections to an array of selectable items for AJAX responses or SPAs
- Utilize the power Laravel's collections for more advanced filtering and sorting

## Installation

You can install the package via composer:

```bash
composer require ringlesoft/laravel-selectable
```

<small> No additional configuration is required.</small>

## Usage

### 1. Basic Usage

```html
<select name="user_id">
    {!! \\App\\Model\\User::all()->toSelectOptions(); !!}}
</select>
```

This will generate a select dropdown with options for all users, using the name field as the label and the id field as
the value.

```bladehtml
<select name="user_id">
    <option value="{{$user->id}}">{{$user->name}}</option>
    ...
</select>
```

### 2. Inline Customization

```bladehtml
<select name="user_id">
    {!! \\App\\Model\\User::all()->toSelectOptions('email', 'uuid', '6490132934f22'); !!}}
</select>
```

This will generate a select dropdown with options for all users, using the `email` field as the label and the `uuid`
field
as the value. The selected option will be the user with the `uuid` 6490132934f22.

```bladehtml
<select name="user_id">
    <option value="{{$user->uuid}}" {{($user->uuid === '6490132934f22') ? 'selected' : '')}}>{{$user->email}}</option>
    ...
</select>
```

#### Method parameters

- `label`: The name of the field to be used as the label for the option. If a closure is provided, the result of the
  closure will be used as the label. Default is `name`.
- `value`: The name of the field to be used as the value for the option. If a closure is provided, the result of the
  closure will be used as the value. Default is `id`.
- `selected`: The value of the item to be used as the selected option. Can be value, array of values or closure.
- `disabled`: The value of the item to be used as the disabled option. Can be value, array of values or closure.

### 3. Advanced Usage

This package allows building of select options from a `Selectable` object using method chaining.
The method `toSelectable()` is used to convert the collection into a `Selectable` object. The `Selectable` object has
several methods that allow you to customize the options and their properties. The `toSelectOptions` method is used to
convert the `Selectable` object into html select options.

```bladehtml
<select name="user_id" multiple="multiple">
    {!!
    \App\Models\User::all()
    ->toSelectable()
    ->withValue('id')
    ->withLabel(fn($user) => "{$user->first_name} {$user->last_name}")
    ->withSelected([2, 3])
    ->withDisabled(fn($item) => $item->status = 'inactive')
    ->withDataAttribute('hidden', fn($item) => $item->status !== 'active')
    ->withClass('form-option custom')
    ->toSelectOptions();
    !!}
</select>
```

This will generate a multi-select dropdown with options for all users, using the `id` field as the `value`, and a
combination of the `first_name` and `last_name` fields as the `label`. Options with IDs `2` and `3` will be selected by
default,
and options with an '`inactive`' `status` will be disabled. A '`data-hidden`' attribute will be added to options with
a `status`
other than '`active`', and a custom class '`form-option custom`' will be applied to all options.

```bladehtml
<select name="user_id" multiple="multiple">
    <option value="1" data-hidden="false" class="form-option custom">David Moore</option>
    <option value="2" data-hidden="false" class="form-option custom">John Doe</option>
    <option value="3" data-hidden="false" class="form-option custom">Jane Doe</option>
    <option value="4" data-hidden="true" class="form-option custom" disabled>Mark Manson</option>
</select>
```

#### Available methods

- `withLabel(string|callable $label)`: This method allows you to customize the label for each option. A string will be
  used as the collection field from which the label will be generated, while a callable will be used to generate the
  label.
- `withValue(string|callable $value)`: This method allows you to customize the value for each option. A string will be
  used as the collection field from which the value will be generated, while a callable will be used to generate the
  value.
- `withSelected(mixed|callable $selected)`: This method allows you to customize the selected options. Can be
  a `string`, `int`, an array of `string`/`int`, a `model` or a callable that returns a boolean value.
- `withDisabled(mixed|callable $disabled)`: This method allows you to customize the disabled options. Can be
  a `string`, `int`, an array of `string`/`int`, a `model` or a callable that returns a boolean value.
- `withDataAttribute(string $attribute, mixed|callable $value)`: This method allows you to add a data attribute to each
  option.
- `withClass(string $class)`: This method allows you to add a class to each option.
- `toSelectItems()`: This method converts the selectable collection to an array of selectable items. Useful for Ajax
  responses or SPA.
- `toSelectOptions()`: This method converts the selectable collection to an HTML select options string.
- Some of the methods from `Illuminate\Support\Collection` are also available including `groupBy()`.

> <small><strong>Note:</strong> Writing queries within blade templates is not recommended. This is only for simplifying
> demonstration</small>

##  Get Selectable Items
```php
    $selectableItems = \App\Models\User::all()->toSelectable()->toSelectItems();
```
This will convert the collection of users into an array of selectable items, which can be useful for AJAX responses or
Single Page Applications (SPAs).

#### Structure of Selectable Items
```json
    [
        [
            'label' => 'User Name',
            'value' => 'user_id',
            'selected' => false,
            'disabled' => false,
            'dataAttributes' => ['hidden' => false],
            'classes' => ['form-option', 'custom'],
        ],
        [...]
    ]
```


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

- [David Ringle](https://github.com/ringunger) (Author)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
