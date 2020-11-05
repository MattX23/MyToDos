<?php

namespace App\Http\Services;

use App\Attachment;
use App\Http\Contracts\HandleFilesContract;
use App\Http\Requests\ToDos\UpdateToDo;
use App\ToDo;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HandleFilesService implements HandleFilesContract
{
    /**
     * @param \Illuminate\Http\UploadedFile $attachment
     * @param \App\ToDo                     $toDo
     */
    public function storeAttachmentWithRelationship(UploadedFile $attachment, ToDo $toDo): void
    {
        $storageName = $this->getUploadName(
            $attachment->getClientOriginalName(),
            $attachment->getClientOriginalExtension(),
            $toDo->user->id
        );
        $storagePath = Storage::putFileAs(Attachment::ATTACHMENT_FILE_PATH, $attachment, $storageName);
        $attachmentDisplayPath = str_replace('public', '', $storagePath);

        Attachment::create([
            'to_do_id'     => $toDo->id,
            'display_name' => $attachment->getClientOriginalName(),
            'file_path'    => $attachmentDisplayPath,
        ]);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param \App\User                     $user
     *
     * @return string
     */
    public function storeImage(UploadedFile $image, User $user): string
    {
        $storageName = $this->getUploadName(
            $image->getClientOriginalName(),
            $image->getClientOriginalExtension(),
            $user->id
        );

        $storagePath = Storage::putFileAs(ToDo::IMAGE_FILE_PATH, $image, $storageName);

        return ToDo::IMAGE_DISPLAY_PATH.basename($storagePath);
    }

    /**
     * @param \App\ToDo $toDo
     * @param bool      $shouldDeleteImage
     * @param bool      $shouldDeleteAttachment
     *
     * @throws \Exception
     */
    public function removeExistingUploadsAndRelationships(ToDo $toDo, bool $shouldDeleteImage, bool $shouldDeleteAttachment): void
    {
        if ($shouldDeleteImage && $toDo->image) $this->removeUpload(
            ToDo::IMAGE_DISPLAY_PATH,
            $toDo->image,
            ToDo::IMAGE_FILE_PATH
        );

        if ($shouldDeleteAttachment && $toDo->attachment) {
            $this->removeUpload(
                ToDo::ATTACHMENT_DISPLAY_PATH,
                $toDo->attachment->file_path,
                ToDo::ATTACHMENT_FILE_PATH
            );

            $toDo->attachment->delete();
        }
    }

    /**
     * @param string $displayPath
     * @param string $fileLocation
     *
     * @param string $filePath
     */
    public function removeUpload(string $displayPath, string $fileLocation, string $filePath): void
    {
        $file = $filePath.'/'.str_replace($displayPath, '', $fileLocation);

        Storage::delete($file);
    }

    /**
     * @param \App\ToDo                               $toDo
     * @param \App\Http\Requests\ToDos\UpdateToDo     $request
     *
     * @throws \Exception
     * @return array
     */
    public function clearStorageForNewUploadsOrOnRequestByUser(ToDo $toDo, UpdateToDo $request): array
    {
        $image = $request->file('image');
        $attachment = $request->file('attachment');

        $shouldDeleteExistingImage = (bool)$image || $request->get('deleteImage');
        $shouldDeleteExistingAttachment = (bool)$attachment || $request->get('deleteAttachment');

        $this->removeExistingUploadsAndRelationships($toDo, $shouldDeleteExistingImage, $shouldDeleteExistingAttachment);

        return array($image, $attachment);
    }

    /**
     * @param string $filename
     * @param string $extension
     * @param int    $userId
     *
     * @return string
     */
    public function getUploadName(string $filename, string $extension, int $userId): string
    {
        return md5($filename.$userId).'.'.$extension;
    }
}