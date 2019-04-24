# PHP wrapper for libsass using FFI

Uses FFI to interact with libsass directly.

## Requirements

* PHP 7.4 or [FFI Extension](https://github.com/dstogov/php-ffi)
* Currently only binaries for linux available (gnu and musl)

## Usage

```php
$compiler = new \ShyimSass\Compiler();

// Set options if wanted
$compiler->setOptions([
    'output_style' => \ShyimSass\Compiler::STYLE_EXPANDED
]);

// Get the compiled string as return value
echo $compiler->compile(__DIR__ . '/test.scss');

// Compile the file into app.css
$compiler->compile(__DIR__ . '/test.scss', __DIR__ . '/app.css');
```