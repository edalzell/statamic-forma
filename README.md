# Give your Statamic addons a beautiful configuration page in the control panel
[![Latest Version](https://img.shields.io/github/release/edalzell/statamic-forma.svg?style=flat-square)](https://github.com/edalzell/statamic-forma/releases)

This package provides an easy way to let users configure your addon.

## Requirements

* PHP 7.4+
* Statamic v3

## Installation

You can install this package via composer using:

```bash
composer require edalzell/forma
```

The package will automatically register itself.

## Usage

First, create a `config.yaml` file in `resources\config` that contains the blueprint for your configuration. As an example, see Mailchimp's, [here](https://github.com/silentzco/statamic-mailchimp/blob/main/resources/blueprints/config.yaml).

Then, in your addon's Service Provider:
* add `use HasConfig`
* in your `boot` method "register" the config:

```
$this->app->booted(function () {
    $this->addConfig('your/package');
});
```

Once you do that, you get a menu item in the cp that your users can access and use. All data is saved into your `addon_handle.php` in the `config` folder.

![menu item](https://raw.githubusercontent.com/edalzell/statamic-forma/main/images/mailchimp-menu.png)

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [addon-security@silentz.co](mailto:addon-security@silentz.co) instead of using the issue tracker.

## License

MIT License
