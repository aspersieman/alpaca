<?php

namespace App\Components\Picture\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\File\Models\File;

/**
 * Class Picture
 * @package App\Components\Picture\Models
 *
 * @property int $id
 * @property int $file_id
 * @property string $name
 * @property string $description
 */
class Picture extends Model
{
    protected $table = 'pictures';

    protected $fillable = [
        'name',
        'for_date',
        'description',
        'file_id'
    ];

    /**
     * the file on this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function file()
    {
        return $this->belongsTo(File::class,'file_id');
    }
}
