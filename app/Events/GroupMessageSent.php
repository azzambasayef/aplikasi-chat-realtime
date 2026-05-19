<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;

    public function __construct(Message $message)
    {
        $message->loadMissing('sender');

        $createdAt = $message->getAttribute('created_at');
        $sender = $message->getRelationValue('sender');

        $this->message = [
            'id' => (int) $message->getKey(),
            'sender_id' => (int) $message->getAttribute('sender_id'),
            'chat_group_id' => (int) $message->getAttribute('chat_group_id'),
            'message' => (string) $message->getAttribute('message'),
            'sender_name' => (string) ($sender?->getAttribute('name') ?? 'User'),
            'created_at' => $createdAt ? $createdAt->format('H:i') : '-',
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-group.' . $this->message['chat_group_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'group.message.sent';
    }
}