<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionText extends Model
{
    protected $table = 'sesion_texts';

    protected $primaryKey = 'id';
    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = ['date', 'textFile', 'user_id', 'sid'];
}
