<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Http\Requests\StoreToDo;
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

        $imageName = $image ? $this->storeImage($image) : null;

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
     * @param \Illuminate\Http\UploadedFile $image
     *
     * @return string
     */
    protected function storeImage(UploadedFile $image): string
    {
        $storagePath = Storage::put(ToDo::IMAGE_FILE_PATH, $image);

        return ToDo::IMAGE_DISPLAY_PATH.basename($storagePath);
    }

    /**
     * @param \Illuminate\Http\UploadedFile $attachment
     * @param \App\ToDo                     $toDo
     */
    protected function storeAttachment(UploadedFile $attachment, ToDo $toDo): void
    {
        $storagePath = Storage::put(Attachment::ATTACHMENT_FILE_PATH, $attachment);

        Attachment::create([
            'to_do_id'     => $toDo->id,
            'display_name' => $attachment->getClientOriginalName(),
            'file_path'    => $storagePath,
        ]);
    }
}
