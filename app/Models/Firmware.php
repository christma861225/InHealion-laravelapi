<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string version
 * @method static create(array $array)
 * @method static where(string $string, mixed $version)
 */
class Firmware extends Model
{
    protected $table = 'firmware';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['version', 'active'];

    public function files(): HasMany
    {
        return $this->hasMany('\App\Models\FirmwareFiles');
    }

    public function path(): string
    {
        return Firmware::getPath($this->version);
    }


    public static function getPath($version): string
    {
        return env('FIRMWARE_PATH') . '/' . $version;
    }
}
