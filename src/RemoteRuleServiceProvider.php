<?php

namespace Maize\RemoteRule;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RemoteRuleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-remote-rule')
            ->hasConfigFile()
            ->hasMigration('create_remote_rule_configs_table');
    }
}
