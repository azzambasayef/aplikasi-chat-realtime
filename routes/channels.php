<?php

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