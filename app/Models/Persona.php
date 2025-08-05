<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'age',
        'occupation',
        'reading_preferences',
        'description',
        'speaking_style',
        'is_active'
    ];

    protected $casts = [
        'reading_preferences' => 'array',
        'is_active' => 'boolean'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
