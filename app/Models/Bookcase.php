<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookcase extends Model
{
    use HasFactory;

    protected $table = 'book_user';

    public function processes(){
        return $this->hasMany(ReadingProcess::class, 'book_user_id', 'id');
    }
}
