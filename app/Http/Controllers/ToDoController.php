<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Http\Requests\StoreToDo;
use App\Http\Requests\UpdateToDo;
use App\ToDo;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ToDoController extends Controller
{
    /**
     * @param \App\User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(User $user): JsonResponse
    {
        return $this->apiResponse($this->getTodos($user));
    }

    /**
     * @param \App\User                    $user
     * @param \App\Http\Requests\StoreToDo $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user, StoreToDo $request): JsonResponse
    {
        $image = $request->file('image');
        $attachment = $request->file('attachment');

        $imageName = $image ? $this->storeImage($image, $user) : null;

        $toDo = ToDo::create([
            'user_id'   => $user->id,
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'due_date'  => $request->get('dueDate'),
            'remind_at' => $request->get('remindAt'),
            'image'     => $imageName,
        ]);

        if ($attachment) $this->storeAttachment($attachment, $toDo);

        return $this->apiResponse($this->getTodos($user));
    }

    /**
     * @param \App\ToDo                     $toDo
     * @param \App\User                     $user
     * @param \App\Http\Requests\UpdateToDo $request
     *
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(ToDo $toDo, User $user, UpdateToDo $request): JsonResponse
    {
        $image = $request->file('image');
        $attachment = $request->file('attachment');

        $this->removeOldUploads($toDo, $image, $attachment);

        $imageName = $image ? $this->storeImage($image, $user) : null;

        $toDo->update([
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'due_date'  => $request->get('dueDate'),
            'remind_at' => $request->get('remindAt'),
            'image'     => $imageName,
        ]);

        if ($attachment) $this->storeAttachment($attachment, $toDo);

        return $this->apiResponse($this->getTodos($user));
    }

    /**
     * @param \App\User $user
     *
     * @return array
     */
    public function getTodos(User $user): array
    {
        $toDos = $user->toDos->groupby('complete');

        return [
            'incomplete' => isset($toDos[0]) ? $toDos[0] : [],
            'complete'   => isset($toDos[1]) ? $toDos[1] : [],
        ];
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReminderDays(): JsonResponse
    {
        return $this->apiResponse(Config::get('options.reminders'));
    }

    /**
     * @param \App\ToDo                          $toDo
     * @param \Illuminate\Http\UploadedFile|null $image
     * @param \Illuminate\Http\UploadedFile|null $attachment
     *
     * @throws \Exception
     */
    protected function removeOldUploads(ToDo $toDo, ?UploadedFile $image, ?UploadedFile $attachment): void
    {
        if ($image && $toDo->image) $this->removeUpload(
            ToDo::IMAGE_DISPLAY_PATH,
            $toDo->image,
            ToDo::IMAGE_FILE_PATH
        );

        if ($attachment && $toDo->attachment) {
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
    protected function removeUpload(string $displayPath, string $fileLocation, string $filePath): void
    {
        $file = $filePath.'/'.str_replace($displayPath, '', $fileLocation);

        Storage::delete($file);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $image
     * @param \App\User                     $user
     *
     * @return string
     */
    protected function storeImage(UploadedFile $image, User $user): string
    {
        $storageName = $this->getUploadName($image->getClientOriginalName(), $image->getClientOriginalExtension(), $user->id);
        $storagePath = Storage::putFileAs(ToDo::IMAGE_FILE_PATH, $image, $storageName);

        return ToDo::IMAGE_DISPLAY_PATH.basename($storagePath);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $attachment
     * @param \App\ToDo                     $toDo
     */
    protected function storeAttachment(UploadedFile $attachment, ToDo $toDo): void
    {
        $storageName = $this->getUploadName($attachment->getClientOriginalName(), $attachment->getClientOriginalExtension(), $toDo->user->id);
        $storagePath = Storage::putFileAs(Attachment::ATTACHMENT_FILE_PATH, $attachment, $storageName);
        $attachmentDisplayPath = str_replace('public', '', $storagePath);

        Attachment::create([
            'to_do_id'     => $toDo->id,
            'display_name' => $attachment->getClientOriginalName(),
            'file_path'    => $attachmentDisplayPath,
        ]);
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
}
