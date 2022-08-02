<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string file_name
 */
class FirmwareFiles extends Model
{
    protected $table = 'firmware_files';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['file_name', 'hash'];

    public function firmware(): BelongsTo
    {
        return $this->belongsTo("\App\Models\Firmware");
    }
}
