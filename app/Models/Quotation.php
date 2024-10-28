<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    public function readingProcess()
    {
        return $this->belongsTo(ReadingProcess::class, 'reading_process_id', 'id');
    }

    public function quotingUser()
    {
        return $this->belongsTo(User::class, 'quoting_user_id', 'id');
    }
}
