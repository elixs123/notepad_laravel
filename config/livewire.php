<?php

return [
    'temporary_file_upload' => [
        'max_upload_size' => 10240,
        'disk' => env('LIVEWIRE_UPLOAD_DISK', 'local'),
    ],

];
