<?php

namespace dnj\Ticket;

use dnj\Filesystem\Contracts\IFile;

trait FileHelpers
{
    protected function saveFile(string $attachFilePath, string $extension, IFile $file): IFile
    {

        $md5Hash = @md5_file($attachFilePath);

        $subDirectories = implode('/', array_slice(str_split($md5Hash, 2), 0, config('ticket.dir_layer_number')));

        $dir = $file->getDirectory()->directory($subDirectories);

        if (!$dir->exists()) {
            $dir->make(true);
        }

        $file->directory .= "/$subDirectories";
        $file->basename = "$md5Hash.$extension";
        $file->write(file_get_contents($attachFilePath));

        return $file;
    }


    protected function deleteFile(IFile $file): bool
    {
        if ($file->exists()) {

            $file->delete();
            return true;
        }

        return false;
    }
}
