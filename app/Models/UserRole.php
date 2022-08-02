<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class UserRole
 * @property array privileges
 * @property string $name
 * @package App\Models
 */
class UserRole extends Model
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_GUEST = 'ROLE_GUEST';

    protected $table = 'user_role';
    public $timestamps = false;

    protected $primaryKey = 'id';

    public function privileges(): HasMany
    {
        return $this->hasMany('\App\Models\UserPrivileges');
    }

    public function users(): HasMany
    {
        return $this->hasMany('\App\Models\User');
    }

}
