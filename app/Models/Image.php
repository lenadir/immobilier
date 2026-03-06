<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'path',
        'disk',
        'original_name',
        'size',
        'mime_type',
        'is_cover',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_cover'   => 'boolean',
            'sort_order' => 'integer',
            'size'       => 'integer',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // ─── Accesseurs ──────────────────────────────────────────────────────────

    /**
     * Retourne l'URL publique complète de l'image.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    protected $appends = ['url'];
}
