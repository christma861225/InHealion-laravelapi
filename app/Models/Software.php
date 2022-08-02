<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property string version
 * @method static create(array $array)
 * @method static where(string $string, mixed $version)
 */
class Software extends Model
{
    protected $table = 'software';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['version', 'active', 'file'];

    public function path(): string
    {
        return Software::getPath($this->version);
    }

    public function fileUrl(): string
    {
        return "/software/" . $this->version . '/download';
    }

    public static function getPath($version): string
    {
        return env('SOFTWARE_PATH') . '/' . $version;
    }
}
