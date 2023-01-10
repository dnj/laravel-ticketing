<?php

namespace dnj\Ticket\Console;

use dnj\Filesystem\Contracts\IFile;
use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Console\Command;

class RemoveTicketAttachment extends Command
{
    protected $signature = 'ticketattachment:remove';

    protected $description = 'Remove the ticket attachments files that not belong to any ticket message';

    public function handle()
    {

        $this->line('Start search for find the files...');

        $files = TicketAttachment::whereNull('message_id')
            ->where('created_at', '<=', now()->subMinutes(10))->get();

        if ($files->count()) {

            $this->info($files->count() . ' file found...');
            $this->newLine();
            $this->line('Start to clean up files...');
            $progress = $this->output->createProgressBar($files->count());
            $progress->start();

            $file = app()->make(IFile::class);

            foreach ($files as $item) {
                $file->unserialize($item->file);
                $file->delete();
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
