<?php

namespace RingleSoft\LaravelSelectable\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use RingleSoft\LaravelSelectable\LaravelSelectableServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [LaravelSelectableServiceProvider::class];
    }
}
