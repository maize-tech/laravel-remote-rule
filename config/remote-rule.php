<?php

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
