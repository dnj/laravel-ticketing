<?php

namespace dnj\Ticket;

use dnj\Ticket\Console\RemoveTicketAttachment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class TicketServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ticket.php', 'ticket');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ticket.php' => config_path('ticket.php'),
            ], 'config');

            $this->commands([
                RemoveTicketAttachment::class,
            ]);

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('ticketattachment:remove')->daily();
            });
        }
    }
}
