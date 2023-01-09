<?php

namespace dnj\Ticket;

use Illuminate\Support\ServiceProvider;
use dnj\Filesystem\Contracts\IFile;
use dnj\Filesystem\Local\File;

class TicketServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ticket.php', 'ticket');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ticket.php' => config_path('ticket.php'),
            ], 'config');
        }

        $this->app->bind(IFile::class, function ($app) {
            return new File(base_path() . '/' . config('ticket.bucket') . '/file');
        });
    }
}
