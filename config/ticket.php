<?php

use dnj\Filesystem\Local\Directory;

return [
    // If set True we will migrate and validate title field for ticket.
    'title' => true,

    // Define your user model class for connect tickets to users.
    'user_model' => null,

    'attachment_root' => new Directory(public_path('ticket')),

    'dir_layer_number' => 2,

    'attachment_rules' => [
        'mimes:jpg,png,txt', 'mimetypes:text/plain,image/jpeg,image/png', 'max:1024',
    ],
];
