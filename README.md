# PHP (Laravel) Knowledge Base v2 (Internal Use)

[![Actions Status](https://github.com/digitalequation/knowledge-base/workflows/Run%20Tests/badge.svg)](https://github.com/digitalequation/knowledge-base/actions)

<h3><span style="color:red">For version 1 of the package check the [v1.md](./v1.md) documentation.</span></h3>

## Installation

You can install the package via composer:

```bash
composer require digitalequation/knowledge-base
```

After the installation is complete, publish the config file:
```bash
php artisan vendor:publish --provider="DigitalEquation\KnowledgeBase\KnowledgeBaseServiceProvider" --tag="config"
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
