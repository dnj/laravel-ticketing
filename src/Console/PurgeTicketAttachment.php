<?php

namespace dnj\Ticket\Console;

use dnj\Ticket\Contracts\IAttachmentManager;
use Illuminate\Console\Command;

class PurgeTicketAttachment extends Command
{
    protected $signature = 'ticket:attachment:purge';

    protected $description = 'Purge the ticket attachments files that not belong to any ticket message';

    public function handle(IAttachmentManager $attachmentManager)
    {
        $this->line('Start search for find the files...');

        $files = $attachmentManager->findOrphans();
        $count = 0;
        if ($files instanceof \Countable) {
            $count = $files->count();
            if (!$count) {
                $this->info('There are no junk files.');

                return;
            }

            $this->info($count.' file found...');
        }
        $progress = $this->output->createProgressBar($count);
        $this->newLine();
        $this->line('Start to clean up files...');
        $progress->start();

        foreach ($files as $item) {
            $attachmentManager->destroy($item->getID());
            $progress->advance();
        }

        $progress->finish();

        $this->newLine(2);
        $this->info('Clearing was done successfully....');
    }
}
