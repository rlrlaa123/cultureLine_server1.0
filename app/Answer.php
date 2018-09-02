<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Answer extends Model
{
    protected $fillable = [
        'contents',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function liked()
    {
        return DB::table('answer_like')->where([
            ['user_id', '=', auth()->user()->id],
            ['answer_id', '=', $this->id],
        ])->first() ? 1 : 0;
    }
}
