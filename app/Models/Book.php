<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function processes()
    {
        return $this->hasMany(ReadingProcess::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_book', 'book_id', 'author_id');
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['q'] ?? false, function (Builder $query, $q) {
            $query->where('title', 'like', '%'.$q.'%');
        });
    }
}
