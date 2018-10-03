<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $fillable = [
        'message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class);
    }
}
