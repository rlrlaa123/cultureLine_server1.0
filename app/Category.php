<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
