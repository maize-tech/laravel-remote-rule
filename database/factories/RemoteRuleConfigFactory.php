<?php

namespace Maize\RemoteRule\Database\Factories;

use Maize\RemoteRule\Models\RemoteRuleConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class RemoteRuleConfigFactory extends Factory
{
    protected $model = RemoteRuleConfig::class;

    public function definition()
    {
        return [
            'name' => 'test',
            'url' => 'url',
            'method' => 'post',
            'headers' => null,
            'json' => null,
            'timeout' => null,
        ];
    }
}
