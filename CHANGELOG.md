## v1.0.6 (2026-07-20)
### Security
* Escape generated option content and attributes to prevent HTML injection.

### Fixed
* Correct selected/disabled matching for arrays of values, models, and arrays.
* Normalize array, object, and grouped collection option data.

### Improved
* Support for Laravel 13
* Add PHPUnit coverage, Composer test command, and Laravel compatibility CI.
* Correct facade namespace casing, IDE helper definitions, and documentation examples.

## v1.0.4 

## v1.0.4 (2025-03-22)
### Added
* Support for IDs: Added support for adding `id` attributes to the selectable items using the `withId` method.

### Improved
* `withDataAttribute` method: Added support for passing a Closure to the `withDataAttribute` method to generate the data attribute value dynamically.
* `withClass` method: Added support for passing an array of classes to the `withClass` method to add multiple classes to the selectable items.
* Performance optimization option rendering 

## v1.0.3 (2024-11-09)
### Added
* Support for non-object (array) collections

### Added
* Support for IDs: Added support for adding IDs to the selectable items using the `withId` method.


## v1.0.2 (2024-07-02)
### Added
* Introduced custom IDE helper file

## v1.0.1 (2024-05-21)

### Added
* Support for grouped collections: Added support for grouping collections using the `groupBy` method.
* Support for data attributes: Added support for specifying data attributes for the selectable items using the `withDataAttribute` method.
* Support for classes: Added support for adding classes to the selectable items using the `withClass` method.

### Improved
* Performance optimization option rendering 

## v1.0.0 (2024-05-20)
First release
