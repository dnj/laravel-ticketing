<?php

namespace dnj\Ticket\Console;

use dnj\Ticket\Contracts\IAttachmentManager;
use Illuminate\Console\Command;

class PurgeTicketAttachment extends Command
{
    protected $signature = 'ticket:attachment:purge';

    protected $description = 'Purge the ticket attachments files that not belong to any ticket message';

    public function handle()
    {
        $this->line('Start search for find the files...');

        $attachment = app()->make(IAttachmentManager::class);
        $files = $attachment->findOrphans();

        if ($files->count()) {
            $this->info($files->count().' file found...');
            $this->newLine();
            $this->line('Start to clean up files...');
            $progress = $this->output->createProgressBar($files->count());
            $progress->start();

            foreach ($files as $item) {
                $item->file->delete();
                $item->delete();
                $progress->advance();
            }

            $progress->finish();

            $this->newLine(2);
            $this->info('Clearing was done successfully....');
        } else {
            $this->info('There are no junk files.');
        }
    }
}
