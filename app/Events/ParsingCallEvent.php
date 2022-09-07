<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ParsingCallEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $data;
    public $uuid;

    /**
     * ParsingCallEvent constructor.
     * @param $data
     * @param $uuid
     */
    public function __construct($data, $uuid)
    {
        $this->data = $data;
        $this->uuid = $uuid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel-' . $this->uuid);
    }
}