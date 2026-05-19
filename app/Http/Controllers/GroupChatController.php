<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupChatController extends Controller
{
    public function store(Request $request)
    {
        $authUserId = (int) Auth::id();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['exists:users,id'],
        ]);

        $group = ChatGroup::create([
            'created_by' => $authUserId,
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        $memberIds = collect($validatedData['member_ids'] ?? [])
            ->push($authUserId)
            ->unique()
            ->values()
            ->toArray();

        $group->users()->sync($memberIds);

        return redirect()->route('group.chat', $group->getKey())
            ->with('success', 'Group chat berhasil dibuat.');
    }

    public function showGroupChat(ChatGroup $chatGroup)
    {
        $authUserId = (int) Auth::id();

        if (!$this->isGroupMember($chatGroup, $authUserId)) {
            return redirect()->route('dashboard')
                ->with('success', 'Kamu bukan anggota group tersebut.');
        }

        $users = User::where('id', '!=', $authUserId)
            ->orderBy('name')
            ->get();

        $chatGroups = ChatGroup::whereHas('users', function ($query) use ($authUserId) {
                $query->where('users.id', $authUserId);
            })
            ->orderBy('name')
            ->get();

        $messages = $chatGroup->messages()
            ->with('sender')
            ->oldest()
            ->get();

        return view('dashboard', [
            'users' => $users,
            'chatGroups' => $chatGroups,
            'selectedGroup' => $chatGroup,
            'messages' => $messages,
        ]);
    }

    public function sendGroupMessage(Request $request, ChatGroup $chatGroup)
    {
        $authUserId = (int) Auth::id();

        if (!$this->isGroupMember($chatGroup, $authUserId)) {
            return redirect()->route('dashboard')
                ->with('success', 'Kamu bukan anggota group tersebut.');
        }

        $validatedData = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        Message::create([
            'sender_id' => $authUserId,
            'chat_group_id' => $chatGroup->getKey(),
            'message' => $validatedData['message'],
        ]);

        return redirect()->route('group.chat', $chatGroup->getKey());
    }

    private function isGroupMember(ChatGroup $chatGroup, int $userId): bool
    {
        return $chatGroup->users()
            ->where('users.id', $userId)
            ->exists();
    }
}