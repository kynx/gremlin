<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Exception;

use Kynx\Gremlin\Structure\Io\IoExceptionInterface;
use Throwable;

use function sprintf;

final class UnderflowException extends \UnderflowException implements IoExceptionInterface
{
    private ?int $expectedLength = null;
    private ?int $actualLength   = null;

    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function emptyStream(): self
    {
        return new self('No more data in stream');
    }

    public static function dataNotRead(int $expected, int $read): self
    {
        $new                 = new self(sprintf("Expected to read %d bytes, received %d", $expected, $read));
        $new->expectedLength = $expected;
        $new->actualLength   = $read;

        return $new;
    }

    public static function dataNotWritten(int $expected, int $written): self
    {
        return new self(sprintf("Expected to write %d bytes, sent %d", $expected, $written));
    }

    public function getExpectedLength(): ?int
    {
        return $this->expectedLength;
    }

    public function getActualLength(): ?int
    {
        return $this->actualLength;
    }
}
