<?php

namespace App\Http\Controllers;

use App\Events\GroupMessageSent;
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

        $selectedGroupMembers = $chatGroup->users()
            ->orderBy('name')
            ->get();

        $canManageSelectedGroup = $this->canManageGroup($chatGroup, $authUserId);

        $availableGroupMembers = collect();

        if ($canManageSelectedGroup) {
            $chatGroupId = (int) $chatGroup->getKey();

            $availableGroupMembers = User::whereDoesntHave('chatGroups', function ($query) use ($chatGroupId) {
                    $query->where('chat_groups.id', $chatGroupId);
                })
                ->orderBy('name')
                ->get();
        }

        return view('dashboard', [
            'users' => $users,
            'chatGroups' => $chatGroups,
            'selectedGroup' => $chatGroup,
            'messages' => $messages,
            'selectedGroupMembers' => $selectedGroupMembers,
            'availableGroupMembers' => $availableGroupMembers,
            'canManageSelectedGroup' => $canManageSelectedGroup,
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

        $message = Message::create([
            'sender_id' => $authUserId,
            'chat_group_id' => $chatGroup->getKey(),
            'message' => $validatedData['message'],
        ]);

        broadcast(new GroupMessageSent($message))->toOthers();

        return redirect()->route('group.chat', $chatGroup->getKey());
    }

    public function addMember(Request $request, ChatGroup $chatGroup)
    {
        $authUserId = (int) Auth::id();

        if (!$this->canManageGroup($chatGroup, $authUserId)) {
            return redirect()->route('group.chat', $chatGroup->getKey())
                ->with('success', 'Hanya pembuat group yang dapat menambahkan anggota.');
        }

        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $userId = (int) $validatedData['user_id'];

        $chatGroup->users()->syncWithoutDetaching([$userId]);

        return redirect()->route('group.chat', $chatGroup->getKey())
            ->with('success', 'Anggota berhasil ditambahkan ke group.');
    }

    public function removeMember(ChatGroup $chatGroup, User $user)
    {
        $authUserId = (int) Auth::id();
        $userId = (int) $user->getKey();
        $groupCreatorId = (int) $chatGroup->getAttribute('created_by');

        if (!$this->canManageGroup($chatGroup, $authUserId)) {
            return redirect()->route('group.chat', $chatGroup->getKey())
                ->with('success', 'Hanya pembuat group yang dapat menghapus anggota.');
        }

        if ($userId === $groupCreatorId) {
            return redirect()->route('group.chat', $chatGroup->getKey())
                ->with('success', 'Pembuat group tidak dapat dihapus dari group.');
        }

        $chatGroup->users()->detach($userId);

        return redirect()->route('group.chat', $chatGroup->getKey())
            ->with('success', 'Anggota berhasil dihapus dari group.');
    }

    private function isGroupMember(ChatGroup $chatGroup, int $userId): bool
    {
        return $chatGroup->users()
            ->where('users.id', $userId)
            ->exists();
    }

    private function canManageGroup(ChatGroup $chatGroup, int $userId): bool
    {
        return (int) $chatGroup->getAttribute('created_by') === $userId;
    }
}