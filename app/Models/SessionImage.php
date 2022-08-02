<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionImage extends Model
{
    protected $table = 'session_images';

    protected $primaryKey = 'id';
    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = ['date', 'sid', 'organ', 'ref_image', 'latest_image'];
}
