<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Http\Requests\ToDos\DeleteToDo;
use App\Http\Requests\ToDos\StoreToDo;
use App\Http\Requests\ToDos\ToDoRequest;
use App\Http\Requests\ToDos\ToggleToDo;
use App\Http\Requests\ToDos\UpdateToDo;
use App\Http\Requests\ToDos\ViewToDo;
use App\ToDo;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ToDoController extends Controller
{
    /**
     * @param \App\User                         $user
     * @param \App\Http\Requests\ToDos\ViewToDo $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(User $user, ViewToDo $request): JsonResponse
    {
        return $this->apiResponse($this->getToDos($user));
    }

    /**
     * @param \App\User                          $user
     * @param \App\Http\Requests\ToDos\StoreToDo $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user, StoreToDo $request): JsonResponse
    {
        $image = $request->file('image');
        $attachment = $request->file('attachment');

        $imageName = $image ? $this->storeImage($image, $user) : null;

        $remindAt = $this->getRemindAtDateTime($request);

        $toDo = ToDo::create([
            'user_id'   => $user->id,
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'due_date'  => $request->get('dueDate'),
            'remind_at' => $remindAt,
            'image'     => $imageName,
        ]);

        if ($attachment) {
            $this->storeAttachment($attachment, $toDo);
        }

        return $this->apiResponse($this->getToDos($user));
    }

    /**
     * @param \App\ToDo                           $toDo
     * @param \App\User                           $user
     * @param \App\Http\Requests\ToDos\UpdateToDo $request
     *
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(ToDo $toDo, User $user, UpdateToDo $request): JsonResponse
    {
        list($image, $attachment) = $this->removeFiles($toDo, $request);

        $imageName = $image ?
            $this->storeImage($image, $user) :
            (
                $request->get('deleteImage') ?
                    null :
                    $toDo->image
            );

        $remindAt = $this->getRemindAtDateTime($request);

        $toDo->update([
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'due_date'  => $request->get('dueDate'),
            'remind_at' => $remindAt,
            'image'     => $imageName,
        ]);

        if ($attachment) $this->storeAttachment($attachment, $toDo);

        return $this->apiResponse($this->getToDos($user));
    }

    /**
     * @param \App\ToDo                           $toDo
     * @param \App\User                           $user
     * @param \App\Http\Requests\ToDos\DeleteToDo $request
     *
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(ToDo $toDo, User $user, DeleteToDo $request): JsonResponse
    {
        $this->removeExistingUploads($toDo, (bool) $toDo->image, (bool) $toDo->attachment()->exists());

        $toDo->delete();

        return $this->apiResponse($this->getToDos($user));
    }

    /**
     * @param \App\User $user
     *
     * @return array
     */
    public function getToDos(User $user): array
    {
        $toDos = $user->toDos->groupby('is_complete');

        return [
            'incomplete' => isset($toDos[0]) ? $toDos[0] : [],
            'complete'   => isset($toDos[1]) ? $toDos[1] : [],
        ];
    }

    /**
     * @param \App\ToDo                           $toDo
     * @param \App\User                           $user
     * @param \App\Http\Requests\ToDos\ToggleToDo $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(ToDo $toDo, User $user, ToggleToDo $request): JsonResponse
    {
        $toDo->update([
           'is_complete' => $request->get('complete'),
        ]);

        return $this->apiResponse($this->getToDos($user));
    }

    /**
     * @param \App\ToDo                           $toDo
     * @param \App\Http\Requests\ToDos\UpdateToDo $request
     *
     * @throws \Exception
     * @return array
     */
    protected function removeFiles(ToDo $toDo, UpdateToDo $request): array
    {
        $image = $request->file('image');
        $attachment = $request->file('attachment');

        $shouldDeleteExistingImage = (bool)$image || $request->get('deleteImage');
        $shouldDeleteExistingAttachment = (bool)$attachment || $request->get('deleteAttachment');

        $this->removeExistingUploads($toDo, $shouldDeleteExistingImage, $shouldDeleteExistingAttachment);

        return array($image, $attachment);
    }

    /**
     * @param \App\ToDo $toDo
     * @param bool      $image
     * @param bool      $attachment
     *
     * @throws \Exception
     */
    protected function removeExistingUploads(ToDo $toDo, bool $image, bool $attachment): void
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
        $storageName = $this->getUploadName(
            $image->getClientOriginalName(),
            $image->getClientOriginalExtension(),
            $user->id
        );

        $storagePath = Storage::putFileAs(ToDo::IMAGE_FILE_PATH, $image, $storageName);

        return ToDo::IMAGE_DISPLAY_PATH.basename($storagePath);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $attachment
     * @param \App\ToDo                     $toDo
     */
    protected function storeAttachment(UploadedFile $attachment, ToDo $toDo): void
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
     * @param string $date
     * @param string $time
     *
     * @return string
     */
    protected function createRemindAtTimeStamp(string $date, string $time): string
    {
        return "$date $time:00";
    }

    /**
     * @param \App\Http\Requests\ToDos\ToDoRequest $request
     *
     * @return string|null
     */
    protected function getRemindAtDateTime(ToDoRequest $request)
    {
        return $request->get('remindAt') ?
            $this->createRemindAtTimeStamp($request->get('remindAt'), $request->get('remindAtTime')) :
            null;
    }
}
