<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string $login
 * @property UserRole $role
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;

    protected $table = 'user';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['login', 'password', 'role', 'one_time_password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'salt'
    ];

    public function role()
    {
        return $this->belongsTo('\App\Models\UserRole');
    }

    public function sessions()
    {
        return $this->hasMany('\App\Models\Session');
    }

    public function profile()
    {
        return $this->hasOne('\App\Models\UserProfile');
    }

    public function hasRole(string $role)
    {
        return $this->role->name === $role;
    }

    public function folders()
    {
        return $this->hasMany('\App\Models\Folder');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
