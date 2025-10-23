<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KtbMember;
use App\Models\KtbGroup;
use App\Models\KtbMemberRelationship;
use Illuminate\Validation\Rule;

class KtbMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = KtbMember::orderBy('id', 'desc')->paginate(15);
        return view('ktb_members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = KtbGroup::where('status', 'active')->orderBy('name')->get();
        return view('ktb_members.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'current_group_id' => ['nullable', 'integer', 'exists:ktb_groups,id'],
            'is_leader' => ['sometimes', 'boolean'],
            'generation' => ['nullable', 'integer', 'min:1'],  // Changed to nullable
            'status' => ['required', Rule::in(['active','inactive','alumni'])],
        ]);

        $member = KtbMember::create($data);

        // Auto-create mentor relationship if assigned to a group with leader
        if ($member->current_group_id && !$member->is_leader) {
            $this->autoAssignMentor($member);
        }

        return redirect()->route('ktb-members.index')->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = KtbMember::with(['mentees.mentoringRelationships', 'mentors.menteeRelationships', 'currentGroup'])
            ->findOrFail($id);
        return view('ktb_members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $member = KtbMember::findOrFail($id);
        $groups = KtbGroup::where('status', 'active')->orderBy('name')->get();
        return view('ktb_members.edit', compact('member', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = KtbMember::findOrFail($id);
        $oldGroupId = $member->current_group_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'current_group_id' => ['nullable', 'integer', 'exists:ktb_groups,id'],
            'is_leader' => ['sometimes', 'boolean'],
            'generation' => ['nullable', 'integer', 'min:1'],  // Changed to nullable
            'status' => ['required', Rule::in(['active','inactive','alumni'])],
        ]);

        $member->update($data);

        // Auto-create mentor relationship if group changed and member is not a leader
        if ($member->current_group_id != $oldGroupId && $member->current_group_id && !$member->is_leader) {
            $this->autoAssignMentor($member);
        }

        return redirect()->route('ktb-members.index')->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = KtbMember::findOrFail($id);
        $member->delete();

        return redirect()->route('ktb-members.index')->with('success', 'Member deleted.');
    }

    /**
     * Show form to add mentee relationship
     */
    public function addMentee(string $id)
    {
        $mentor = KtbMember::findOrFail($id);

        // Get available members that are not already mentees
        $existingMenteeIds = $mentor->mentees()->pluck('ktb_members.id')->toArray();

        $availableMembers = KtbMember::where('status', 'active')
            ->where('id', '!=', $mentor->id)
            ->whereNotIn('id', $existingMenteeIds)
            ->orderBy('name')
            ->get();

        $groups = KtbGroup::where('status', 'active')->orderBy('name')->get();

        return view('ktb_members.add_mentee', compact('mentor', 'availableMembers', 'groups'));
    }

    /**
     * Store mentee relationship
     */
    public function storeMentee(Request $request, string $id)
    {
        $mentor = KtbMember::findOrFail($id);

        $validated = $request->validate([
            'mentee_id' => 'required|exists:ktb_members,id',
            'group_id' => 'nullable|exists:ktb_groups,id',
            'status' => ['required', Rule::in(['rutin', 'tidak rutin', 'dipotong'])],
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'notes' => 'nullable|string',
        ]);

        // Check if relationship already exists
        $exists = KtbMemberRelationship::where('mentor_id', $mentor->id)
            ->where('mentee_id', $validated['mentee_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['mentee_id' => 'Relasi mentoring dengan anggota ini sudah ada.']);
        }

        KtbMemberRelationship::create([
            'mentor_id' => $mentor->id,
            'mentee_id' => $validated['mentee_id'],
            'group_id' => $validated['group_id'],
            'status' => $validated['status'],
            'started_at' => $validated['started_at'],
            'ended_at' => $validated['ended_at'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('ktb-members.show', $mentor)
            ->with('success', 'Mentee berhasil ditambahkan!');
    }

    /**
     * Show form to edit mentee relationship
     */
    public function editMentee(string $memberId, string $relationshipId)
    {
        $mentor = KtbMember::findOrFail($memberId);
        $relationship = KtbMemberRelationship::with('mentee')->findOrFail($relationshipId);
        $groups = KtbGroup::where('status', 'active')->orderBy('name')->get();

        return view('ktb_members.edit_mentee', compact('mentor', 'relationship', 'groups'));
    }

    /**
     * Update mentee relationship
     */
    public function updateMentee(Request $request, string $memberId, string $relationshipId)
    {
        $relationship = KtbMemberRelationship::findOrFail($relationshipId);

        $validated = $request->validate([
            'group_id' => 'nullable|exists:ktb_groups,id',
            'status' => ['required', Rule::in(['rutin', 'tidak rutin', 'dipotong'])],
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'notes' => 'nullable|string',
        ]);

        $relationship->update($validated);

        return redirect()->route('ktb-members.show', $memberId)
            ->with('success', 'Relasi mentoring berhasil diperbarui!');
    }

    /**
     * Delete mentee relationship
     */
    public function destroyMentee(string $memberId, string $relationshipId)
    {
        $relationship = KtbMemberRelationship::findOrFail($relationshipId);
        $relationship->delete();

        return redirect()->route('ktb-members.show', $memberId)
            ->with('success', 'Relasi mentoring berhasil dihapus!');
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
