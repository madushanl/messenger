<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PublicChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Message object
     * @var \App\Message
     */
    private $message;

    /**
     * Broadcast channel
     * @var string
     */
    private $channel;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $chat)
    {
        $this->message = $message;
        $this->channel = $chat->channel_identifier;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->channel);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'new-message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'user_id' => $this->message->user_id,
            'chat_id' => $this->message->chat_id,
            'user' => $this->message->user,
            'body' => $this->message->body,
            'created_at' => $this->message->created_at->format('Y-m-d H:i:s')
        ];
    }
}
