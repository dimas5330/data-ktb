<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KtbMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'birth_date',
        'gender',
        'current_group_id',
        'is_leader',
        'joined_at',
        'generation',
        'status',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joined_at' => 'date',
        'is_leader' => 'boolean',
        'generation' => 'integer',
    ];

    /**
     * Relasi ke user (jika ada)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke kelompok KTB saat ini
     */
    public function currentGroup(): BelongsTo
    {
        return $this->belongsTo(KtbGroup::class, 'current_group_id');
    }

    /**
     * Kelompok yang dipimpin (jika dia leader)
     */
    public function leadingGroups(): HasMany
    {
        return $this->hasMany(KtbGroup::class, 'leader_id');
    }

    /**
     * Relasi ke adik-adik KTB (mentees) - orang yang dibimbing
     */
    public function mentees(): BelongsToMany
    {
        return $this->belongsToMany(
            KtbMember::class,
            'ktb_member_relationships',
            'mentor_id',
            'mentee_id'
        )
        ->withPivot(['group_id', 'started_at', 'ended_at', 'status', 'notes'])
        ->withTimestamps()
        ->using(KtbMemberRelationship::class);
    }

    /**
     * Relasi ke kakak-kakak KTB (mentors) - pembimbing
     */
    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(
            KtbMember::class,
            'ktb_member_relationships',
            'mentee_id',
            'mentor_id'
        )
        ->withPivot(['group_id', 'started_at', 'ended_at', 'status', 'notes'])
        ->withTimestamps()
        ->using(KtbMemberRelationship::class);
    }

    /**
     * Semua relasi sebagai mentor
     */
    public function mentoringRelationships(): HasMany
    {
        return $this->hasMany(KtbMemberRelationship::class, 'mentor_id');
    }

    /**
     * Semua relasi sebagai mentee
     */
    public function menteeRelationships(): HasMany
    {
        return $this->hasMany(KtbMemberRelationship::class, 'mentee_id');
    }

    /**
     * Get all active mentees
     */
    public function activeMentees()
    {
        return $this->mentees()->wherePivot('status', 'active');
    }

    /**
     * Get total mentees count
     */
    public function getTotalMenteesCountAttribute(): int
    {
        return $this->mentees()->count();
    }

    /**
     * Get active mentees count
     */
    public function getActiveMenteesCountAttribute(): int
    {
        return $this->activeMentees()->count();
    }

    /**
     * Check if member has opened new KTB group (has become a leader)
     */
    public function hasOpenedNewGroup(): bool
    {
        return $this->leadingGroups()->exists();
    }

    /**
     * Get all descendants (recursive) - semua keturunan KTB
     */
    public function getAllDescendants()
    {
        $descendants = collect();

        foreach ($this->mentees as $mentee) {
            $descendants->push($mentee);
            $descendants = $descendants->merge($mentee->getAllDescendants());
        }

        return $descendants->unique('id');
    }

    /**
     * Get tree structure for this member
     */
    public function getTreeStructure()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_leader' => $this->is_leader,
            'current_group' => $this->currentGroup?->name,
            'leading_groups' => $this->leadingGroups->pluck('name'),
            'total_direct_mentees' => $this->mentees()->count(),
            'mentees' => $this->mentees->map(function ($mentee) {
                return $mentee->getTreeStructure();
            }),
        ];
    }

    /**
     * Calculate generation from mentor chain
     */
    public function calculateGeneration(): int
    {
        // Jika tidak punya mentor = Generation 1 (Root)
        if ($this->mentors()->count() === 0) {
            return 1;
        }

        // Generation = Max(mentor generations) + 1
        $maxMentorGen = $this->mentors()->max('generation');

        return ($maxMentorGen ?? 0) + 1;
    }

    /**
     * Calculate and update generation
     */
    public function calculateAndUpdateGeneration(): int
    {
        $newGeneration = $this->calculateGeneration();

        // Only update if different
        if ($this->generation !== $newGeneration) {
            $this->update(['generation' => $newGeneration]);
        }

        return $newGeneration;
    }

    /**
     * Get generation attribute with fallback to calculation
     */
    public function getGenerationAttribute($value)
    {
        // If generation exists, return it
        if ($value !== null) {
            return $value;
        }

        // Otherwise calculate it
        return $this->calculateGeneration();
    }
}
