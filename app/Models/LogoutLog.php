<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogoutLog extends Model
{
    protected $fillable = ['rfid_no', 'time', 'date'];

    protected $casts = [
        'time' => 'datetime:H:i:s',
        'date' => 'date',
    ];
}
