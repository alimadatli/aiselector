<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelectorChange extends Model
{
    protected $fillable = [
        'selector_id',
        'old_selector',
        'new_selector',
        'reason',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function selector(): BelongsTo
    {
        return $this->belongsTo(Selector::class);
    }
}
