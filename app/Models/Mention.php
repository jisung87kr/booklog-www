<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function readingProcess()
    {
        return $this->belongsTo(ReadingProcess::class, 'reading_process_id', 'id');
    }
}
