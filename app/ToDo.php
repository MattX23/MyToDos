<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $user_id
 * @property $title
 * @property $body
 * @property $due_date
 * @property $remind_at
 * @property $complete
 * @property $image
 */
class ToDo extends Model implements Arrayable
{
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toArray(): array
    {
        return [
            'user_id'   => $this->user_id,
            'title'     => $this->title,
            'body'      => $this->body,
            'due_date'  => $this->due_date,
            'remind_at' => $this->remind_at,
            'complete'  => $this->complete,
            'image'     => $this->image,
        ];
    }
}
