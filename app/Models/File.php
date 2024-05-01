<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'size',
        'type',
        'slideshow_id'
    ];
    
    public function slideshow()
    {
        return $this->belongsTo(Slideshow::class);
    }

}
