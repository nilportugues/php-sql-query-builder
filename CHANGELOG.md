# Changelog

## 1.0.2 - TBA

### Added

- Added @shadowhand (Woody Gilk) to the **composer.json** under authors.
- Comments for queries using the **setComment** method.
- Table aliasing.

### Altered

- Changed PSR-0 loading to PSR-4.
- Changed the class loading in GenericBuilder. Now a classmap is used to load the query builder classes only when required.
- Changed test method names to camelCase format.
- Normalized the way select, insert and update behave internally.


## 1.0.1 - 2014-09-23

### Altered
- Big change in class loading to reducing memory usage.

## 1.0.0 - 2014-09-13

- First stable release

## 0.0.5-alpha - 2014-07-01

- Initial release