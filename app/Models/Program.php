<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Folder folder
 * @property string name
 */
class Program extends Model
{
    protected $table = 'program';

    protected $primaryKey = 'id';
    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = ['name', 'hash', 'active'];

    public function folder(): BelongsTo
    {
        return $this->belongsTo('\App\Models\Folder');
    }

    public function fileName(): string
    {
        return $this->folder->path() . '/' . $this->name;
    }
}
