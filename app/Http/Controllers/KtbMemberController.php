<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KtbMember;
use App\Models\KtbGroup;
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
            'generation' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['active','inactive','alumni'])],
        ]);

        $member = KtbMember::create($data);

        return redirect()->route('ktb-members.index')->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = KtbMember::findOrFail($id);
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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'current_group_id' => ['nullable', 'integer', 'exists:ktb_groups,id'],
            'is_leader' => ['sometimes', 'boolean'],
            'generation' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['active','inactive','alumni'])],
        ]);

        $member->update($data);

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
}
