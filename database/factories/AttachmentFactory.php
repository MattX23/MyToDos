<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Attachment;

$factory->define(Attachment::class, function () {
    $fileNumber = rand(1, 4);

    return [
        'display_name' => 'file'.$fileNumber.'.pdf',
        'file_path'    => '/attachments/seed_files/file'.$fileNumber.'.pdf',
    ];
});
