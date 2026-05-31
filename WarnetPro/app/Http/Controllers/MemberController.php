<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\PlaySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Member::orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(15);

        return view('members.index', compact('members', 'search'));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:members,username|alpha_dash',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:4|confirmed',
        ]);

        Member::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => $request->password,
            'status' => 'active',
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Member berhasil dibuat!');
    }

    /**
     * Display the member detail and usage history.
     */
    public function show(Member $member)
    {
        $sessions = PlaySession::where('member_id', $member->id)
            ->with('computer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('members.show', compact('member', 'sessions'));
    }

    /**
     * Show the form for editing a member.
     */
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified member.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'username' => 'required|string|max:255|alpha_dash|unique:members,username,' . $member->id,
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:4|confirmed',
            'status' => 'required|in:active,suspended',
        ]);

        $data = [
            'username' => $request->username,
            'name' => $request->name,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $member->update($data);

        return redirect()->route('members.show', $member)
            ->with('success', 'Member berhasil diupdate!');
    }

    /**
     * Add time to member account.
     */
    public function addTime(Request $request, Member $member)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1|max:1440',
        ]);

        $member->addTime((int) $request->minutes);

        return redirect()->back()
            ->with('success', $request->minutes . ' menit berhasil ditambahkan ke akun ' . $member->name . '!');
    }

    /**
     * Reset password for a member.
     */
    public function resetPassword(Request $request, Member $member)
    {
        $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        $member->update([
            'password' => $request->password,
        ]);

        return redirect()->back()
            ->with('success', 'Password berhasil direset!');
    }

    /**
     * Toggle member status (active/suspended).
     */
    public function toggleStatus(Member $member)
    {
        $member->update([
            'status' => $member->status === 'active' ? 'suspended' : 'active',
        ]);

        return redirect()->back()
            ->with('success', 'Status member berhasil diubah!');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member berhasil dihapus!');
    }
}
