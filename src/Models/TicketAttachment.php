<?php

namespace dnj\Ticket\Models;

use dnj\Filesystem\Contracts\IFile;
use dnj\Filesystem\Local;
use dnj\Ticket\Casts\File;
use dnj\Ticket\Database\Factories\TicketAttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class TicketAttachment extends Model
{
    use HasFactory;

    public static function fromUpload(UploadedFile $file): self
    {
        $model = new TicketAttachment();
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getClientMimeType();
        $model->size = filesize($file->path());

        return $model;
    }

    public static function putFileInStorage(UploadedFile $input): IFile
    {
        $tmpFile = new Local\File($input->path());
        $hash = $tmpFile->md5();
        $path = implode('/', array_slice(str_split($hash, 2), 0, config('ticket.dir_layer_number')))."/{$hash}.".$input->getExtension();
        $file = config('ticket.attachment_root')->file($path);
        $dir = $file->getDirectory();
        if (!$dir->exists()) {
            $dir->make(true);
        }
        $tmpFile->move($file);

        return $file;
    }

    protected static function newFactory()
    {
        return TicketAttachmentFactory::new();
    }

    protected $fillable = ['message_id', 'name', 'file', 'mime', 'size'];
    protected $hidden = ['file'];
    protected $table = 'tickets_attachments';

    protected $casts = [
        'file' => File::class,
    ];

    public function putFile(UploadedFile|IFile $file)
    {
        if ($file instanceof UploadedFile) {
            $file = self::putFileInStorage($file);
        }
        $this->file = $file;
    }
}
