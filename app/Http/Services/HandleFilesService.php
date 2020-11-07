<?php

namespace App\Http\Services;

use App\Attachment;
use App\Http\Contracts\HandleFilesContract;
use App\ToDo;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HandleFilesService implements HandleFilesContract
{
    /**
     * @param \Illuminate\Http\UploadedFile $attachment
     * @param \App\ToDo                     $toDo
     *
     * @return string
     */
    public function storeAttachment(UploadedFile $attachment, ToDo $toDo): string
    {
        $storageName = $this->getUploadName(
            $attachment->getClientOriginalName(),
            $attachment->getClientOriginalExtension(),
            $toDo->user->id
        );

        $storagePath = Storage::putFileAs(Attachment::ATTACHMENT_FILE_PATH, $attachment, $storageName);

        return str_replace('public', '', $storagePath);
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
     * @param string    $type
     *
     * @throws \Exception
     */
    public function removeFile(ToDo $toDo, string $type): void
    {
        if ($type === ToDo::IMAGE && $toDo->image && $this->isNotATestFile($toDo->image)) {
            $this->deleteFile(
                ToDo::IMAGE_DISPLAY_PATH,
                $toDo->image,
                ToDo::IMAGE_FILE_PATH
            );
        }

        if ($type === ToDo::ATTACHMENT && $toDo->attachment && $this->isNotATestFile($toDo->attachment->file_path)) {
            $this->deleteFile(
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
    protected function deleteFile(string $displayPath, string $fileLocation, string $filePath): void
    {
        $file = $filePath.'/'.str_replace($displayPath, '', $fileLocation);

        Storage::delete($file);
    }

    /**
     * @param string $filename
     * @param string $extension
     * @param int    $userId
     *
     * @return string
     */
    protected function getUploadName(string $filename, string $extension, int $userId): string
    {
        return md5($filename.$userId).'.'.$extension;
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    protected function isNotATestFile(string $filePath): bool
    {
        return !Str::contains($filePath, 'seed_files');
    }
}