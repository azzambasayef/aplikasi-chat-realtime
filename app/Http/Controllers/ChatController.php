<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function showPrivateChat(User $user)
    {
        $authUserId = (int) Auth::id();
        $selectedUserId = (int) $user->getKey();

        if ($selectedUserId === $authUserId) {
            return redirect()->route('dashboard')->with('success', 'Tidak bisa membuka chat dengan akun sendiri.');
        }

        $users = User::where('id', '!=', $authUserId)
            ->orderBy('name')
            ->get();

        $conversation = $this->getOrCreateConversation($user);

        $messages = $conversation->messages()
            ->with('sender')
            ->oldest()
            ->get();

        return view('dashboard', [
            'users' => $users,
            'selectedUser' => $user,
            'selectedConversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function sendPrivateMessage(Request $request, User $user)
    {
        $authUserId = (int) Auth::id();
        $selectedUserId = (int) $user->getKey();

        if ($selectedUserId === $authUserId) {
            return redirect()->route('dashboard')->with('success', 'Tidak bisa mengirim pesan ke akun sendiri.');
        }

        $validatedData = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $conversation = $this->getOrCreateConversation($user);

        Message::create([
            'sender_id' => $authUserId,
            'conversation_id' => $conversation->getKey(),
            'message' => $validatedData['message'],
        ]);

        return redirect()->route('private.chat', $selectedUserId);
    }

    private function getOrCreateConversation(User $user): Conversation
    {
        $authUserId = (int) Auth::id();
        $selectedUserId = (int) $user->getKey();

        $conversation = Conversation::where(function ($query) use ($authUserId, $selectedUserId) {
                $query->where('user_one_id', $authUserId)
                    ->where('user_two_id', $selectedUserId);
            })
            ->orWhere(function ($query) use ($authUserId, $selectedUserId) {
                $query->where('user_one_id', $selectedUserId)
                    ->where('user_two_id', $authUserId);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $authUserId,
                'user_two_id' => $selectedUserId,
            ]);
        }

        return $conversation;
    }
}