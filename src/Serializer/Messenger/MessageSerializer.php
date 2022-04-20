<?php

declare(strict_types=1);

namespace Yanarowana123\NotificationQueueBundle\Serializer\Messenger;

use RuntimeException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

final class MessageSerializer extends Serializer
{
    private $params;

    public function __construct(
        array $params,
        SymfonySerializerInterface $serializer = null,
        string $format = 'json',
        array $context = []
    ) {
        parent::__construct($serializer, $format, $context);
        $this->params = $params;
    }

    public function decode(array $encodedEnvelope): Envelope
    {

        if (array_key_exists('type', $encodedEnvelope[ 'headers' ]) === false) {
            throw new MessageDecodingFailedException('Encoded envelope does not have a "type" header.');
        }

        $type = $encodedEnvelope[ 'headers' ][ 'type' ];

        $classMessage = $this->params[ $type ];

        $encodedEnvelope[ 'headers' ][ 'type' ] = $classMessage;

        return parent::decode($encodedEnvelope);
    }

    public function encode(Envelope $envelope): array
    {
        $data = parent::encode($envelope);

        $type = $data[ 'headers' ][ 'type' ];

        $key = array_search($type, $this->params);

        if ($key === false) {
            throw new RuntimeException(
                sprintf(
                    'Key with class %s not found in array!',
                    $key
                )
            );
        }

        $data[ 'headers' ][ 'type' ] = $key;

        return $data;
    }
}
