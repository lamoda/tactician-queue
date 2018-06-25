<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Serializer\Serializer;

final class CommandSerializationHandler implements SubscribingHandlerInterface
{
    /** @var Serializer */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize(JsonSerializationVisitor $visitor, $command, array $type, Context $context): array
    {
        return [
            'command' => get_class($command),
            'properties' => json_decode($this->serializer->serialize($command, 'json'), true),
        ];
    }

    public function deserialize(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->serializer->deserialize(json_encode($data['properties']), $data['command'], 'json');
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'tactician_command',
                'method' => 'serialize',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'tactician_command',
                'method' => 'deserialize',
            ],
        ];
    }
}
