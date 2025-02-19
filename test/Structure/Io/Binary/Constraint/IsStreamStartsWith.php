<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use Psr\Http\Message\StreamInterface;

final class IsStreamStartsWith extends Constraint
{
    private readonly IsIdentical $isIdentical;

    public function __construct(mixed $value, private readonly int $length = 1)
    {
        if ($value instanceof StreamInterface) {
            $value->rewind();
            $value = $value->read($this->length);
        }

        $this->isIdentical = new IsIdentical($value);
    }

    public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
    {
        if ($other instanceof StreamInterface) {
            $other->rewind();
            $other = $other->read($this->length);
        }

        return $this->isIdentical->evaluate($other, $description, $returnResult);
    }

    public function toString(): string
    {
        return $this->isIdentical->toString();
    }
}
