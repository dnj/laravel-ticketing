<?php

return [
    // If set True we will migrate and validate title field for ticket.
    'title' => true,

    // Define your user model class for connect tickets to users.
    'user_model' => null,

    'bucket' => 'public/ticket',

    'dir_layer_number' => 2,

    'attachment_rules' => [
        'required', 'file', 'mimes:jpg,png,txt', 'mimetypes:text/plain,image/jpeg,image/png', 'max:1024'
    ],
];
