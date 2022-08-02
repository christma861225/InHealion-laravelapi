<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @property string name
 * @property Program[] programs
 * @property bool is_encrypted
 */
class Folder extends Model
{
    protected $table = 'folder';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['name', 'expires_in', 'active', 'is_encrypted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo('\App\Models\User');
    }

    public function programs(): HasMany
    {
        return $this->hasMany('\App\Models\Program');
    }

    public function path(): string
    {
        return Folder::getFolderPath($this->name, $this->user->id);
    }


    public static function getFolderPath($name, $userId): string
    {
        return env('PROGRAM_PATH') . '/' . $userId . '/' . $name;
    }
}
