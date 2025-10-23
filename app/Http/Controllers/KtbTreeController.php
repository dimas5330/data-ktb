<?php

namespace App\Http\Controllers;

use App\Models\KtbMember;
use App\Models\KtbGroup;
use Illuminate\Http\Request;

class KtbTreeController extends Controller
{
    public function index()
    {
        // Get all members with their relationships
        $members = KtbMember::with(['mentees', 'mentors', 'currentGroup'])->get();

        return view('ktb_tree.index', compact('members'));
    }

    public function getTreeData()
    {
        // Find root members (members without mentors or generation 1)
        $rootMembers = KtbMember::whereDoesntHave('mentors')
            ->orWhere('generation', 1)
            ->with(['mentees', 'currentGroup'])
            ->get();

        $treeData = [];
        foreach ($rootMembers as $root) {
            $treeData[] = $this->buildTreeNode($root);
        }

        return response()->json($treeData);
    }

    private function buildTreeNode(KtbMember $member)
    {
        $node = [
            'id' => $member->id,
            'name' => $member->name,
            'generation' => $member->generation,
            'status' => $member->status,
            'group' => $member->currentGroup?->name,
            'email' => $member->email,
            'phone' => $member->phone,
            'children' => []
        ];

        // Get active mentees
        $mentees = $member->mentees()->get();

        foreach ($mentees as $mentee) {
            $node['children'][] = $this->buildTreeNode($mentee);
        }

        return $node;
    }

    public function showMemberTree($id)
    {
        $member = KtbMember::with(['mentees', 'mentors', 'currentGroup'])->findOrFail($id);
        $treeData = $this->buildTreeNode($member);

        return view('ktb_tree.member', compact('member', 'treeData'));
    }
}
