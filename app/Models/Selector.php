<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Website;
use App\Models\SelectorChange;

class Selector extends Model
{
    protected $fillable = [
        'website_id',
        'name',
        'selector',
        'description',
        'is_active'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(SelectorChange::class);
    }
}
