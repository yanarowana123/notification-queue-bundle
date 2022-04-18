<?php

declare(strict_types=1);

namespace Like\NotificationQueueBundle\Messenger;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Symfony\Component\Serializer\SerializerInterface;

use function get_class;

final class AuditMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->logger = new NullLogger();
        $this->serializer = $serializer;
    }

    /**
     * @throws \JsonException
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->last(UniqueIdStamp::class) === null) {
            $envelope = $envelope->with(new UniqueIdStamp());
        }

        /** @var UniqueIdStamp $stamp */
        $stamp = $envelope->last(UniqueIdStamp::class);

        $context = json_decode(
            $this->serializer->serialize($envelope->getMessage(), 'json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $envelope = $stack->next()->handle($envelope, $stack);

        if ($envelope->last(ReceivedStamp::class)) {
            $this->logger->info(
                sprintf('[%s] Received %s', $stamp->getUniqueId(), get_class($envelope->getMessage())),
                $context
            );
        } elseif ($envelope->last(SentStamp::class)) {
            $this->logger->info(
                sprintf('[%s] Received %s', $stamp->getUniqueId(), get_class($envelope->getMessage())),
                $context
            );
        } else {
            $this->logger->info(
                sprintf('[%s] Handling sync %s', $stamp->getUniqueId(), get_class($envelope->getMessage())),
                $context
            );
        }

        return $envelope;
    }

}
