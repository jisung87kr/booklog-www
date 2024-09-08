<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['url'];

    public function attachmentable(){
        return $this->morphTo('attachmentable');
    }

    public function getUrlAttribute()
    {
        return config('app.url').Storage::url($this->file_path);
    }
}
