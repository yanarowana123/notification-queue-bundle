<?php

declare(strict_types=1);

namespace Yanarowana123\NotificationQueueBundle\Messenger;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Stamp\StampInterface;

final class UniqueIdStamp implements StampInterface
{
    private $uniqueId;

    public function __construct()
    {
        $this->uniqueId = Uuid::uuid4()->toString();
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

}
