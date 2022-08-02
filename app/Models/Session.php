<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'session';

    protected $primaryKey = 'id';
    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = ['token', 'expires_in', 'active'];
}
