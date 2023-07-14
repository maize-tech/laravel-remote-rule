<p align="center">
<picture>
  <source media="(prefers-color-scheme: dark)" srcset="/art/socialcard-dark.png">
  <source media="(prefers-color-scheme: light)" srcset="/art/socialcard-light.png">
  <img src="/art/socialcard-light.png" alt="Social Card of Laravel Remote Rule">
</picture>
</p>

# Laravel Remote Rule

[![Latest Version on Packagist](https://img.shields.io/packagist/v/maize-tech/laravel-remote-rule.svg?style=flat-square)](https://packagist.org/packages/maize-tech/laravel-remote-rule)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/maize-tech/laravel-remote-rule/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/maize-tech/laravel-remote-rule/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/maize-tech/laravel-remote-rule/php-cs-fixer.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/maize-tech/laravel-remote-rule/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/maize-tech/laravel-remote-rule.svg?style=flat-square)](https://packagist.org/packages/maize-tech/laravel-remote-rule)

Easily validate data attributes through a remote request.

This package allows you to define a subset of custom rules to validate an incoming request data through an api call to a remote service.

An example usage could be an application with an open registration form which should only allow a list of emails given by a remote service.
You can find an example in the [Usage](#usage) section.

## Installation

You can install the package via composer:

```bash
composer require maize-tech/laravel-remote-rule
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Maize\RemoteRule\RemoteRuleServiceProvider" --tag="remote-rule-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Maize\RemoteRule\RemoteRuleServiceProvider" --tag="remote-rule-config"
```

This is the content of the published config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Config model
    |--------------------------------------------------------------------------
    |
    | Here you may specify the fully qualified class name of the config model.
    |
    */

    'config_model' => Maize\RemoteRule\Models\RemoteRuleConfig::class,

    /*
    |--------------------------------------------------------------------------
    | Attribute cast
    |--------------------------------------------------------------------------
    |
    | Here you may specify the cast type for all model attributes who contain
    | sensitive data.
    | All attributes listed below will be encrypted by default when creating or
    | updating a model instance. You can disable this behaviour by removing
    | the attribute cast from the array.
    |
    */

    'attribute_cast' => [
        'url' => 'encrypted',
        'method' => 'encrypted',
        'headers' => 'encrypted:array',
        'json' => 'encrypted:array',
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug mode
    |--------------------------------------------------------------------------
    |
    | Here you may enable or disable the debug mode. If enabled, the rule will
    | throw an exception instead of validating the attribute.
    |
    */

    'debug' => false,

    /*
    |--------------------------------------------------------------------------
    | Validation message
    |--------------------------------------------------------------------------
    |
    | Here you may specify the message thrown if the validation rule fails.
    |
    */

    'validation_message' => 'The :attribute must be valid.',
];
```

## Usage

### Basic

To use the package you can create a class which extends the `RemoteRule` abstract class.

```php
use Maize\RemoteRule\RemoteRule;

class EmailRule extends RemoteRule
{
    //
}
```

You can then create a new `RemoteRuleConfig` instance with the snake case name of the class you just created.
The model will contain the url of the remote service used to send the request along with the request method (usually `POST` or `GET`) and, if needed, the custom headers, json body and timeout.

We add this data to a database table as we consider it sensitive information, and we want to avoid hard-coding it to the codebase.
All entries of the `remote_rule_configs` database table are indeed encrypted by default (can be disabled in the configs).

You can create a remote rule config in your console with tinker:

```bash
php artisan tinker
```

```php
\Maize\RemoteRule\Models\RemoteRuleConfig::query()->create([
    'name' => 'email_rule',
    'url' => 'test.example.com',
    'method' => 'POST',
    'headers' => [], // can be null if no custom headers are required
    'json' => [], // can be null if no custom json body is required
    'timeout' => 10, // can be null if you want to use the default timeout
]);
```

That's all! You can now just add your new custom rule to the validation array:

```php
use Illuminate\Support\Facades\Validator;

$email = 'my-email@example.com';

Validator::make([
    'email' => $email,
], [
    'email' => [
        'string',
        'email',
        new EmailRule,
    ],
])->validated(); 
```

Under the hood, the validation rule will send a request to the given url with the custom headers and body (where we append the attribute name and its value) and check whether the response is successful or not.

### Custom response status code

You can change the expected response status code by overriding the `isSuccessful` method in your custom rule.

```php
use Maize\RemoteRule\RemoteRule;
use Illuminate\Http\Client\Response;

class EmailRule extends RemoteRule
{
    protected function isSuccessful(Response $response): bool
    {
        return $response->status() === 204;
    }
}
```

### Custom body attribute

By default, all custom rules will append the attribute key-value pair to the request body, where the key is the name of the validated attribute, and the value is the input value of the form.

You can change the body attribute sent to the remote service by overriding the `getBodyAttribute` method in your custom rule.

```php
use Maize\RemoteRule\RemoteRule;

class EmailRule extends RemoteRule
{
    protected function getBodyAttribute(): array
    {
        return ['custom_attribute' => $this->value];
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/maize-tech/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](https://github.com/maize-tech/.github/security/policy) on how to report security vulnerabilities.

## Credits

- [Enrico De Lazzari](https://github.com/enricodelazzari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
