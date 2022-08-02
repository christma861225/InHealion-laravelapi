<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserPrivileges extends Model
{
    const MANAGE_PROFILES = 'manage-profiles';
    const MANAGE_PROGRAMS = 'manage-programs';
    const CREATE_USER = 'create-user';
    const VIEW_PROFILE = 'view-profile';
    const VIEW_PROGRAMS = 'view-programs';
    const MANAGE_FIRMWARE = 'manage-firmware';
    const MANAGE_SOFTWARE = 'manage-software';

    protected $table = 'user_privileges';
    public $timestamps = false;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['name'];

    public function userRole()
    {
        return $this->belongsTo('\App\Model\UserRole');
    }
}
