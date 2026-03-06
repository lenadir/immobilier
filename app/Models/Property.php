<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    // ─── Constantes ──────────────────────────────────────────────────────────

    public const TYPES = ['appartement', 'villa', 'terrain', 'bureau', 'commerce', 'maison', 'studio'];
    public const STATUSES = ['disponible', 'vendu', 'location'];

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'rooms',
        'surface',
        'price',
        'city',
        'address',
        'description',
        'status',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'rooms'        => 'integer',
            'surface'      => 'float',
            'price'        => 'float',
            'is_published' => 'boolean',
        ];
    }

    // ─── Événements : génération automatique du titre ────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $property): void {
            $property->title = $property->generateTitle();
        });

        static::updating(function (self $property): void {
            // Regénère si un champ pertinent a changé
            if ($property->isDirty(['type', 'rooms', 'city', 'status'])) {
                $property->title = $property->generateTitle();
            }
        });
    }

    /**
     * Génère le titre automatiquement.
     * Exemples :
     *   - "Villa 4 pièces à Alger"
     *   - "Appartement 2 pièces à Oran - En location"
     *   - "Terrain 500m² à Constantine"
     */
    public function generateTitle(): string
    {
        $type  = ucfirst($this->type ?? '');
        $parts = [$type];

        if (!empty($this->rooms) && !in_array($this->type, ['terrain', 'bureau', 'commerce'])) {
            $parts[] = $this->rooms . ' pièce' . ($this->rooms > 1 ? 's' : '');
        }

        if ($this->type === 'terrain' && !empty($this->surface)) {
            $parts[] = number_format($this->surface, 0, ',', ' ') . 'm²';
        }

        if (!empty($this->city)) {
            $parts[] = 'à ' . ucwords($this->city);
        }

        $title = implode(' ', $parts);

        if ($this->status === 'location') {
            $title .= ' - En location';
        } elseif ($this->status === 'vendu') {
            $title .= ' - Vendu';
        }

        return $title;
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('sort_order');
    }

    public function coverImage(): HasMany
    {
        return $this->hasMany(Image::class)->where('is_cover', true)->limit(1);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCity($query, ?string $city)
    {
        return $city ? $query->where('city', 'like', "%{$city}%") : $query;
    }

    public function scopeByType($query, ?string $type)
    {
        return $type ? $query->where('type', $type) : $query;
    }

    public function scopeByStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeByPriceRange($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (!$keyword) {
            return $query;
        }
        return $query->whereFullText(['title', 'description'], $keyword);
    }
}
