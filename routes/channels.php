<?php

use App\Models\ChatGroup;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    $authUserId = (int) $user->getKey();
    $userOneId = (int) $conversation->getAttribute('user_one_id');
    $userTwoId = (int) $conversation->getAttribute('user_two_id');

    return $authUserId === $userOneId || $authUserId === $userTwoId;
});

Broadcast::channel('chat-group.{chatGroupId}', function (User $user, int $chatGroupId) {
    $chatGroup = ChatGroup::find($chatGroupId);

    if (!$chatGroup) {
        return false;
    }

    return $chatGroup->users()
        ->where('users.id', (int) $user->getKey())
        ->exists();
});

Broadcast::channel('online-users', fn (User $user) => [
    'id' => (int) $user->getKey(),
    'name' => (string) $user->getAttribute('name'),
    'email' => (string) $user->getAttribute('email'),
]);