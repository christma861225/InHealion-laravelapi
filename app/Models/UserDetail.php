<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_details';

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = ['login', 'password', 'surname', 'address', 'phone_number', 'email', 'dob', 'history', 'postcode', 'details'];

}
