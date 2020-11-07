<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Http\Contracts\HandleFilesContract;
use App\Http\Requests\ToDos\DeleteToDo;
use App\Http\Requests\ToDos\StoreToDo;
use App\Http\Requests\ToDos\ToDoRequest;
use App\Http\Requests\ToDos\ToggleToDo;
use App\Http\Requests\ToDos\UpdateToDo;
use App\Http\Requests\ToDos\ViewToDo;
use App\ToDo;
use App\User;
use Illuminate\Http\JsonResponse;

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
     * @param \App\User                               $user
     * @param \App\Http\Requests\ToDos\StoreToDo      $request
     * @param \App\Http\Contracts\HandleFilesContract $handleFilesService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user, StoreToDo $request, HandleFilesContract $handleFilesService): JsonResponse
    {
        $image = $request->file('image');
        $attachment = $request->file('attachment');

        $imageName = $image ? $handleFilesService->storeImage($image, $user) : null;

        $toDo = ToDo::create([
            'user_id'   => $user->id,
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'due_date'  => $request->get('dueDate'),
            'remind_at' => $this->getRemindAtDateTime($request),
            'image'     => $imageName,
        ]);

        if ($attachment) {
            $attachmentDisplayPath = $handleFilesService->storeAttachment($attachment, $toDo);

            Attachment::create([
                'to_do_id'     => $toDo->id,
                'display_name' => $attachment->getClientOriginalName(),
                'file_path'    => $attachmentDisplayPath,
            ]);
        }

        return $this->apiResponse($this->getToDos($user));
    }

    /**
     * @param \App\ToDo                               $toDo
     * @param \App\User                               $user
     * @param \App\Http\Requests\ToDos\UpdateToDo     $request
     * @param \App\Http\Contracts\HandleFilesContract $handleFilesService
     *
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(ToDo $toDo, User $user, UpdateToDo $request, HandleFilesContract $handleFilesService): JsonResponse
    {
        $image = $request->file('image');
        $attachment = $request->file('attachment');

        if ((bool) $image || $request->get('deleteImage')) {
            $handleFilesService->removeFile(
                $toDo,
                ToDo::IMAGE
            );
        }

        if ((bool) $attachment || $request->get('deleteAttachment')) {
            $handleFilesService->removeFile(
                $toDo,
                ToDo::ATTACHMENT
            );
        }

        $imageName = $image ?
            $handleFilesService->storeImage($image, $user) :
            (
                $request->get('deleteImage') ?
                    null :
                    $toDo->image
            );

        $toDo->update([
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'due_date'  => $request->get('dueDate'),
            'remind_at' => $this->getRemindAtDateTime($request),
            'image'     => $imageName,
        ]);

        if ($attachment) $handleFilesService->storeAttachment($attachment, $toDo);

        return $this->apiResponse($this->getToDos($user));
    }

    /**
     * @param \App\ToDo                               $toDo
     * @param \App\User                               $user
     * @param \App\Http\Requests\ToDos\DeleteToDo     $request
     * @param \App\Http\Contracts\HandleFilesContract $handleFilesService
     *
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(
        ToDo $toDo,
        User $user,
        DeleteToDo $request,
        HandleFilesContract $handleFilesService
    ): JsonResponse {
        $this->cleanUpFiles($toDo, $handleFilesService);

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

    /**
     * @param \App\ToDo                               $toDo
     * @param \App\Http\Contracts\HandleFilesContract $handleFilesService
     */
    protected function cleanUpFiles(ToDo $toDo, HandleFilesContract $handleFilesService): void
    {
        if ($toDo->image) {
            $handleFilesService->removeFile(
                $toDo,
                ToDo::IMAGE
            );
        }

        if ($toDo->attachment) {
            $handleFilesService->removeFile(
                $toDo,
                ToDo::ATTACHMENT
            );
        }
    }
}
