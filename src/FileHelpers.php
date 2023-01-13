<?php

namespace dnj\Ticket;

use dnj\Filesystem\Contracts\IFile;
use Illuminate\Http\UploadedFile;

trait FileHelpers
{
    protected function saveFile(UploadedFile $attachFile): IFile
    {
        $extension = $attachFile->extension();
        $file = config('ticket.attachment_root')->file('new');
        $tmpFile = config('ticket.attachment_root')->file('tmp');

        $tmpFile->directory = $attachFile->getPath();
        $tmpFile->basename = $attachFile->getBasename();

        $md5Hash = $tmpFile->md5(false);

        $subDirectories = implode('/', array_slice(str_split($md5Hash, 2), 0, config('ticket.dir_layer_number')));

        $dir = $file->getDirectory()->directory($subDirectories);

        if (!$dir->exists()) {
            $dir->make(true);
        }

        $file->directory .= "/$subDirectories";
        $file->basename = "$md5Hash.$extension";

        $tmpFile->copyTo($file);

        return $file;
    }

    protected function deleteFile(IFile $file): void
    {
        $file->delete();
    }
}
