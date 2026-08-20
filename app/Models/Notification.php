<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'created_by',
    ];

    /**
     * The admin who created this notification.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Users who received this notification (pivot tracks read status).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Scope: filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Type badge color mapping for Bootstrap.
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'success' => 'success',
            'warning' => 'warning',
            'danger'  => 'danger',
            default   => 'info',
        };
    }

    /**
     * Type icon mapping for Bootstrap Icons.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'success' => 'bi-check-circle-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            'danger'  => 'bi-x-circle-fill',
            default   => 'bi-info-circle-fill',
        };
    }
}
