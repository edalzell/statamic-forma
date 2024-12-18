# Give your Statamic addons a beautiful configuration page in the control panel
[![Latest Version](https://img.shields.io/github/release/edalzell/statamic-forma.svg?style=flat-square)](https://github.com/edalzell/statamic-forma/releases)

This package provides an easy way to let users configure your addon.

## Requirements

* PHP 8.2+
* Laravel 10.0+
* Statamic 4.0+

## Installation

You can install this package via composer using:

```bash
composer require edalzell/forma
```

The package will automatically register itself.

## Usage

First, create a `config.yaml` file in `resources\blueprints` that contains the blueprint for your configuration. As an example, see Mailchimp's, [here](https://github.com/statamic-rad-pack/mailchimp/blob/main/resources/blueprints/config.yaml).

Then, in the `bootAddon` method of your addon's Service Provider add:
```php
\Edalzell\Forma\Forma::add('statamic-rad-pack/mailchimp', ConfigController::class);
```

The second parameter is optional and only needed if you need custom config handling (see Extending below)

There is a 3rd parameter `handle` you can use if the config file is NOT the addon's handle.

Once you do that, you get a menu item in the cp that your users can access and use. All data is saved into your `addon_handle.php` (or `$handle` as per above) in the `config` folder.

![menu item](https://raw.githubusercontent.com/edalzell/statamic-forma/main/images/mailchimp-menu.png)

### Permissions

There is a `Manage Addon Settings` permission that must be enabled to allow a user to update the settings of any Forma-enabled addons.

### Extending

If your addon needs to wangjangle the config before loading and after saving, create your own controller that `extends \Edalzell\Forma\ConfigController` and use the `preProcess` and `postProcess` methods.

For example, the Mailchimp addon stores a config like this:
```php
'user' => [
    'check_consent' => true,
    'consent_field' => 'permission',
    'merge_fields' => [
        [
            'field_name' => 'first_name',
        ],
    ],
    'disable_opt_in' => true,
    'interests_field' => 'interests',
],
```

But there is no Blueprint that supports that, so it uses a grid, which expects the data to look like:
```php
'user' => [
    [
        'check_consent' => true,
        'consent_field' => 'permission',
        'merge_fields' => [
            [
                'field_name' => 'first_name',
            ],
        ],
        'disable_opt_in' => true,
        'interests_field' => 'interests',
    ]
],
```

Therefore in its `ConfigController`:
```php
protected function postProcess(array $values): array
{
    $userConfig = Arr::get($values, 'user');

    return array_merge(
        $values,
        ['user' => $userConfig[0]]
    );
}

protected function preProcess(string $handle): array
{
    $config = config($handle);

    return array_merge(
        $config,
        ['user' => [Arr::get($config, 'user', [])]]
    );
}
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [addon-security@silentz.co](mailto:addon-security@silentz.co) instead of using the issue tracker.

## License

MIT License
