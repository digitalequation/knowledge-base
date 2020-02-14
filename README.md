# PHP (Laravel) Knowledge Base (Internal Use)

[![Actions Status](https://github.com/digitalequation/knowledge-base/workflows/Run%20Tests/badge.svg)](https://github.com/digitalequation/knowledge-base/actions)

## Installation

You can install the package via composer:

```bash
composer require digitalequation/knowledge-base
```

After the installation is complete, from your project's root run:
```bash
php artisan knowledge-base:install
```

This will publish all the config file for the package.

## Usage

Available commands:  
**NOTE:** passing `--force` to the command will overwrite the already published files.
``` php
# Publish the config file
php artisan knowledge-base:config
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email robert.chiribuc@thebug.ro instead of using the issue tracker.

## Credits

- [Robert Cristian Chiribuc](https://github.com/chiribuc)
- [Marcel Mihai Bontaș](https://github.com/kirov117)
- [All Contributors](../../contributors)
