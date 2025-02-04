<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary;

use Kynx\Gremlin\Structure\Io\Binary\ReaderException;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\GraphType;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Psr\Http\Message\StreamInterface;

final readonly class Reader
{
    /** @var array<int, SerializerInterface> */
    private array $readers;

    public function __construct(SerializerInterface ...$serializers)
    {
        $readers = [];
        foreach ($serializers as $serializer) {
            $readers[$serializer->getGraphType()->value] = $serializer;
        }

        $this->readers = $readers;
    }

    public function getReader(StreamInterface $stream): SerializerInterface
    {
        return $this->getReaderForType(GraphType::fromStream($stream));
    }

    public function getReaderForType(GraphType $type): SerializerInterface
    {
        if (! isset($this->readers[$type->value])) {
            throw ReaderException::unsupportedBinaryType($type->value);
        }

        return $this->readers[$type->value];
    }
}
