<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['image_url'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getImageUrlAttribute()
    {
        return config('app.url').Storage::url($this->file_path);
    }
}
