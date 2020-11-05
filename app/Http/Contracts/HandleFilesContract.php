<?php

namespace App\Http\Contracts;

use App\Http\Requests\ToDos\UpdateToDo;
use App\ToDo;
use App\User;
use Illuminate\Http\UploadedFile;

interface HandleFilesContract
{
    /**
     * @param \Illuminate\Http\UploadedFile $attachment
     * @param \App\ToDo                     $toDo
     */
    public function storeAttachmentWithRelationship(UploadedFile $attachment, ToDo $toDo): void;

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param \App\User                     $user
     *
     * @return string
     */
    public function storeImage(UploadedFile $image, User $user): string;

    /**
     * @param string $filename
     * @param string $extension
     * @param int    $userId
     *
     * @return string
     */
    public function getUploadName(string $filename, string $extension, int $userId): string;

    /**
     * @param \App\ToDo $toDo
     * @param bool      $shouldDeleteImage
     * @param bool      $shouldDeleteAttachment
     *
     * @throws \Exception
     */
    public function removeExistingUploadsAndRelationships(ToDo $toDo, bool $shouldDeleteImage, bool $shouldDeleteAttachment): void;

    /**
     * @param \App\ToDo                               $toDo
     * @param \App\Http\Requests\ToDos\UpdateToDo     $request
     *
     * @throws \Exception
     * @return array
     */
    public function clearStorageForNewUploadsOrOnRequestByUser(ToDo $toDo, UpdateToDo $request): array;

    /**
     * @param string $displayPath
     * @param string $fileLocation
     *
     * @param string $filePath
     */
    public function removeUpload(string $displayPath, string $fileLocation, string $filePath): void;
}