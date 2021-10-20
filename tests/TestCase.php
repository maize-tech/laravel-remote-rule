<?php

namespace Maize\RemoteRule\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Maize\RemoteRule\RemoteRuleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Maize\\RemoteRule\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            RemoteRuleServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'N5OtbYwarRpmNeyHOU4amEBNRVzBCKAZ');
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__.'/../database/migrations/create_remote_rule_configs_table.php.stub';
        (new \CreateRemoteRuleConfigsTable())->up();
    }
}
