<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KtbGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'leader_id',
        'description',
        'location',
        'meeting_day',
        'meeting_time',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'meeting_time' => 'datetime:H:i',
    ];

    /**
     * Relasi ke leader/pemimpin kelompok
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(KtbMember::class, 'leader_id');
    }

    /**
     * Relasi ke semua anggota kelompok
     */
    public function members(): HasMany
    {
        return $this->hasMany(KtbMember::class, 'current_group_id');
    }

    /**
     * Relasi ke semua relasi mentoring dalam kelompok ini
     */
    public function relationships(): HasMany
    {
        return $this->hasMany(KtbMemberRelationship::class, 'group_id');
    }

    /**
     * Get total members count
     */
    public function getMembersCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Check if group is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
