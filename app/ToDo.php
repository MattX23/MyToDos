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
 * @property string     $remind_at_time
 * @property bool       $is_complete
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
        'attachment'   => 'nullable|file|max:4096',
        'body'         => 'nullable|string',
        'dueDate'      => 'nullable|date|required_with:remindAt|after:today',
        'image'        => 'nullable|image|max:4096',
        'remindAt'     => 'nullable|exclude_if:dueDate,null|date|after:tomorrow|before:dueDate',
        'remindAtTime' => 'required_with:remindAt|date_format:H:i',
        'title'        => 'required|string|min:2',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'body',
        'is_complete',
        'due_date',
        'image',
        'remind_at',
        'title',
        'user_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_complete' => 'bool',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'attachment',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'remind_at_time',
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

    public static function boot() : void
    {
        parent::boot();

        static::deleting(function(ToDo $toDo) {
            $toDo->attachment()->delete();
        });
    }

    /**
     * @return string
     */
    public function getRemindAtTimeAttribute(): string
    {
        $time = substr($this->remind_at, 11, 2);

        return $time && $time[0] === "0" ? $time[1] : $time;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attachment'     => $this->attachment,
            'body'           => $this->body,
            'is_complete'    => $this->is_complete,
            'due_date'       => $this->due_date,
            'id'             => $this->id,
            'image'          => $this->image,
            'remind_at'      => substr($this->remind_at, 0, 10),
            'remind_at_time' => $this->remind_at_time,
            'title'          => $this->title,
            'user_id'        => $this->user_id,
        ];
    }
}
