<?php

namespace dnj\Ticket\Tests;

use dnj\Ticket\Tests\Models\User;
use dnj\Ticket\TicketServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config()->set('ticket.user_model', User::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            TicketServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
