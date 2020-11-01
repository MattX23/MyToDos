<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int        $user_id
 * @property int        $id
 * @property string     $title
 * @property string     $body
 * @property string     $due_date
 * @property string     $remind_at
 * @property bool       $complete
 * @property string     $image
 * @property Attachment $attachment
 * @property User       $user
 */
class ToDo extends Model implements Arrayable
{
    const ATTACHMENT_DISPLAY_PATH = '/attachments/';
    const ATTACHMENT_FILE_PATH = 'public/attachments';
    const IMAGE_FILE_PATH = '/public/images';
    const IMAGE_DISPLAY_PATH = '/images/';
    const RULES = [
        'attachment' => 'nullable|file|max:4096',
        'body'       => 'nullable|string',
        'dueDate'    => 'nullable|date|required_with:remindAt|after:today',
        'image'      => 'nullable|image|max:4096',
        'remindAt'   => 'nullable|exclude_if:dueDate,null|date|after:tomorrow',
        'title'      => 'required|string|min:2',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'complete',
        'due_date',
        'image',
        'remind_at',
        'title',
        'user_id',
    ];

    /**
     * @var array
     */
    protected $with = [
        'attachment'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attachment(): HasOne
    {
        return $this->hasOne(Attachment::class);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attachment' => $this->attachment,
            'body'       => $this->body,
            'complete'   => $this->complete,
            'due_date'   => $this->due_date,
            'id'         => $this->id,
            'image'      => $this->image,
            'remind_at'  => $this->remind_at,
            'title'      => $this->title,
            'user_id'    => $this->user_id,
        ];
    }
}
