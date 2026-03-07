<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        // Force all tests onto the dedicated test database.
        // This overrides whatever .env loaded so the dev database is never touched.
        $app['config']->set('database.connections.mysql.database', 'sinfat_portfolio_test');

        return $app;
    }
}
