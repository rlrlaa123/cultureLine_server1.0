<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'contents',
    ];

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
