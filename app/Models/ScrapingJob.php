<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Website;

class ScrapingJob extends Model
{
    protected $fillable = [
        'website_id',
        'scraped_data',
        'failed_selectors',
        'html_content',
        'status',
        'error_message'
    ];

    protected $casts = [
        'scraped_data' => 'array',
        'failed_selectors' => 'array'
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
