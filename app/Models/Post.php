<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_draft',
        'published_at',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk postingan yang sudah terbit
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_draft', false)
            ->where('published_at', '<=', Carbon::now());
    }
}
