<?php

namespace dnj\Ticket;

use dnj\Ticket\Console\PurgeTicketAttachment;
use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Contracts\ITicketAttachmentManager;
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Contracts\ITicketMessageManager;
use dnj\Ticket\Managers\DepartmentManager;
use dnj\Ticket\Managers\TicketAttachmentManager;
use dnj\Ticket\Managers\TicketManager;
use dnj\Ticket\Managers\TicketMessageManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class TicketServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ticket.php', 'ticket');
        $this->app->register('dnj\UserLogger\ServiceProvider');
        $this->app->bind(IDepartmentManager::class, DepartmentManager::class);
        $this->app->bind(ITicketManager::class, TicketManager::class);
        $this->app->bind(ITicketMessageManager::class, TicketMessageManager::class);
        $this->app->bind(ITicketAttachmentManager::class, TicketAttachmentManager::class);
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
                PurgeTicketAttachment::class,
            ]);

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('ticket:attachment:purge')->daily();
            });
        }
    }
}
