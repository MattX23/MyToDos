<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $user_id
 * @property string $title
 * @property string $body
 * @property string $due_date
 * @property string $remind_at
 * @property bool $complete
 * @property string $image
 * @property Attachment $attachment
 */
class ToDo extends Model implements Arrayable
{
    const IMAGE_FILE_PATH = '/public/images/';
    const IMAGE_DISPLAY_PATH = '/images/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'due_date',
        'remind_at',
        'complete',
        'image',
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id'    => $this->user_id,
            'title'      => $this->title,
            'body'       => $this->body,
            'due_date'   => $this->due_date,
            'remind_at'  => $this->remind_at,
            'complete'   => $this->complete,
            'image'      => $this->image,
            'attachment' => $this->attachment,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attachment(): HasOne
    {
        return $this->hasOne(Attachment::class);
    }
}
