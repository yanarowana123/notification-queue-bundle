<?php

namespace Like\NotificationQueueBundle\Message;

final class PushNotificationMessage
{
    public function __construct(
        private readonly string $channel,
        private readonly array $data
    )
    {
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getData(): array
    {
        return $this->data;
    }

}
