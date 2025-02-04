<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

final readonly class Writer
{
    /** @var array<class-string<TypeInterface>, SerializerInterface> */
    private array $writers;

    public function __construct(
        SerializerInterface ...$serializers
    ) {
        $writers = [];

        foreach ($serializers as $serializer) {
            $writers[$serializer->getPhpType()] = $serializer;
        }

        $this->writers = $writers;
    }

    public function write(StreamInterface $stream, TypeInterface $type): void
    {
        if (! isset($this->writers[$type::class])) {
            throw WriterException::unsupportedPhpType($type);
        }

        $writer = $this->writers[$type::class];
        $stream->write($writer->getGraphType()->getByte());
        $writer->write($stream, $type, $this);
    }
}
