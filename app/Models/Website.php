<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Website extends Model
{
    protected $fillable = [
        'name',
        'url',
        'is_active',
        'user_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function selectors(): HasMany
    {
        return $this->hasMany(Selector::class);
    }

    public function scrapingJobs(): HasMany
    {
        return $this->hasMany(ScrapingJob::class);
    }
}
