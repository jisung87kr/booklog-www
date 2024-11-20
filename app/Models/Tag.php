<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function taggable()
    {
        return $this->morphTo('taggable');
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['q'] ?? false, function (Builder $query, $q) {
            $query->where('name', 'like', '%'.$q.'%');
        });
    }
}
