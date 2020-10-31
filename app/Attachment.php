<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $display_name
 * @property string $file_path
 */
class Attachment extends Model implements Arrayable
{
    const ATTACHMENT_FILE_PATH = '/public/attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'to_do_id',
        'display_name',
        'file_path',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(ToDo::class);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'display_name' => $this->display_name,
            'file_path'    => $this->file_path,
        ];
    }
}
