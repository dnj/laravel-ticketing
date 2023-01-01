<?php

namespace dnj\Ticket\Tests;

use dnj\Ticket\TicketServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            TicketServiceProvider::class
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }
}
