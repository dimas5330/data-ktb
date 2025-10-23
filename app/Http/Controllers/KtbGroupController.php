<?php

namespace App\Http\Controllers;

use App\Models\KtbGroup;
use App\Models\KtbMember;
use App\Models\KtbMemberRelationship;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KtbGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = KtbGroup::with(['leader', 'members'])
            ->withCount('members')
            ->latest()
            ->paginate(15);

        return view('ktb_groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = KtbMember::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('ktb_groups.create', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'leader_id' => 'nullable|exists:ktb_members,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'meeting_day' => ['nullable', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'meeting_time' => 'nullable|date_format:H:i',
            'status' => ['required', Rule::in(['active', 'inactive', 'completed'])],
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
        ]);

        $group = KtbGroup::create($validated);

        // Update leader's is_leader flag
        if ($request->leader_id) {
            KtbMember::where('id', $request->leader_id)->update(['is_leader' => true]);
        }

        return redirect()->route('ktb-groups.index')
            ->with('success', 'Kelompok KTB berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(KtbGroup $ktbGroup)
    {
        $ktbGroup->load(['leader', 'members.mentees', 'members.mentors']);

        return view('ktb_groups.show', compact('ktbGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KtbGroup $ktbGroup)
    {
        $members = KtbMember::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('ktb_groups.edit', compact('ktbGroup', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KtbGroup $ktbGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'leader_id' => 'nullable|exists:ktb_members,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'meeting_day' => ['nullable', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'meeting_time' => 'nullable|date_format:H:i',
            'status' => ['required', Rule::in(['active', 'inactive', 'completed'])],
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
        ]);

        // Update old leader's flag
        if ($ktbGroup->leader_id && $ktbGroup->leader_id != $request->leader_id) {
            KtbMember::where('id', $ktbGroup->leader_id)->update(['is_leader' => false]);
        }

        $ktbGroup->update($validated);

        // Update new leader's flag
        if ($request->leader_id) {
            KtbMember::where('id', $request->leader_id)->update(['is_leader' => true]);
        }

        return redirect()->route('ktb-groups.index')
            ->with('success', 'Kelompok KTB berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KtbGroup $ktbGroup)
    {
        // Update leader flag before deleting
        if ($ktbGroup->leader_id) {
            KtbMember::where('id', $ktbGroup->leader_id)->update(['is_leader' => false]);
        }

        $ktbGroup->delete();

        return redirect()->route('ktb-groups.index')
            ->with('success', 'Kelompok KTB berhasil dihapus!');
    }

    /**
     * Show form to assign members to group
     */
    public function assignMembers(KtbGroup $ktbGroup)
    {
        $ktbGroup->load('members');

        $availableMembers = KtbMember::where('status', 'active')
            ->whereNull('current_group_id')
            ->orWhere('current_group_id', $ktbGroup->id)
            ->orderBy('name')
            ->get();

        return view('ktb_groups.assign_members', compact('ktbGroup', 'availableMembers'));
    }

    /**
     * Update member assignments
     */
    public function updateMembers(Request $request, KtbGroup $ktbGroup)
    {
        $validated = $request->validate([
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:ktb_members,id',
        ]);

        // Remove current group from all existing members
        KtbMember::where('current_group_id', $ktbGroup->id)
            ->update(['current_group_id' => null]);

        // Assign selected members to this group
        if (!empty($validated['member_ids'])) {
            $members = KtbMember::whereIn('id', $validated['member_ids'])->get();

            foreach ($members as $member) {
                $member->update(['current_group_id' => $ktbGroup->id]);

                // Auto-assign mentor if member is not a leader
                if (!$member->is_leader) {
                    $this->autoAssignMentor($member);
                }
            }
        }

        return redirect()->route('ktb-groups.show', $ktbGroup)
            ->with('success', 'Anggota kelompok berhasil diperbarui!');
    }

    /**
     * Auto-assign mentor based on group leader
     */
    private function autoAssignMentor(KtbMember $member)
    {
        // Refresh member to get latest data
        $member->refresh();
        
        // Find group leader first
        $leader = KtbMember::where('current_group_id', $member->current_group_id)
            ->where('is_leader', true)
            ->where('id', '!=', $member->id)
            ->first();

        if (!$leader) {
            // If no leader, try to find the ACTUAL leader of the group
            $group = KtbGroup::with('leader')->find($member->current_group_id);
            
            if ($group && $group->leader_id && $group->leader_id != $member->id) {
                $leader = KtbMember::find($group->leader_id);
            }
        }

        if (!$leader) {
            // Last resort: find senior member in same group
            // Priority: lowest generation, then earliest ID
            $leader = KtbMember::where('current_group_id', $member->current_group_id)
                ->where('id', '!=', $member->id)
                ->whereNotNull('generation')
                ->orderBy('generation', 'asc')
                ->orderBy('id', 'asc')
                ->first();
        }

        if ($leader) {
            // Check if relationship already exists
            $exists = KtbMemberRelationship::where('mentor_id', $leader->id)
                ->where('mentee_id', $member->id)
                ->exists();

            if (!$exists) {
                // Create mentor-mentee relationship
                KtbMemberRelationship::create([
                    'mentor_id' => $leader->id,
                    'mentee_id' => $member->id,
                    'group_id' => $member->current_group_id,
                    'status' => 'rutin',
                    'started_at' => now(),
                    'notes' => 'Auto-assigned when joining group',
                ]);
                
                // Force recalculate generation after relationship created
                $member->refresh();
                $member->calculateAndUpdateGeneration();
            }
        }
    }
}
