<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class KtbMemberRelationship extends Pivot
{
    protected $table = 'ktb_member_relationships';

    public $incrementing = true;

    protected $fillable = [
        'mentor_id',
        'mentee_id',
        'group_id',
        'started_at',
        'ended_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    /**
     * Relasi ke mentor (Kakak KTB)
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(KtbMember::class, 'mentor_id');
    }

    /**
     * Relasi ke mentee (Adik KTB)
     */
    public function mentee(): BelongsTo
    {
        return $this->belongsTo(KtbMember::class, 'mentee_id');
    }

    /**
     * Relasi ke kelompok
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(KtbGroup::class, 'group_id');
    }

    /**
     * Check if relationship is rutin (active)
     */
    public function isRutin(): bool
    {
        return $this->status === 'rutin';
    }

    /**
     * Check if relationship is dipotong (terminated)
     */
    public function isDipotong(): bool
    {
        return $this->status === 'dipotong';
    }
}
