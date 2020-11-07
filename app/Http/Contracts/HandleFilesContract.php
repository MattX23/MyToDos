<?php

namespace App\Http\Contracts;

use App\ToDo;
use App\User;
use Illuminate\Http\UploadedFile;

interface HandleFilesContract
{
    /**
     * @param \Illuminate\Http\UploadedFile $attachment
     * @param \App\ToDo                     $toDo
     *
     * @return string
     */
    public function storeAttachment(UploadedFile $attachment, ToDo $toDo): string;

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param \App\User                     $user
     *
     * @return string
     */
    public function storeImage(UploadedFile $image, User $user): string;


    /**
     * @param \App\ToDo $toDo
     * @param string    $type
     */
    public function removeFile(ToDo $toDo, string $type): void;
}