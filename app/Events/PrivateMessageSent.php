<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;

    public function __construct(Message $message)
    {
        $message->loadMissing('sender');

        $createdAt = $message->getAttribute('created_at');

        $this->message = [
            'id' => (int) $message->getKey(),
            'sender_id' => (int) $message->getAttribute('sender_id'),
            'conversation_id' => (int) $message->getAttribute('conversation_id'),
            'message' => (string) $message->getAttribute('message'),
            'sender_name' => (string) ($message->sender?->getAttribute('name') ?? 'User'),
            'created_at' => $createdAt ? $createdAt->format('H:i') : '-',
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->message['conversation_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'private.message.sent';
    }
}