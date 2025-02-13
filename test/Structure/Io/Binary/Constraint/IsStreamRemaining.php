<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use Psr\Http\Message\StreamInterface;

final class IsStreamRemaining extends Constraint
{
    private readonly IsIdentical $isIdentical;

    public function __construct(mixed $value)
    {
        if ($value instanceof StreamInterface) {
            $value = $value->read($value->getSize() ?? 8192);
        }

        $this->isIdentical = new IsIdentical($value);
    }

    public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
    {
        if ($other instanceof StreamInterface) {
            $other = $other->read($other->getSize() ?? 8192);
        }

        return $this->isIdentical->evaluate($other, $description, $returnResult);
    }

    public function toString(): string
    {
        return $this->isIdentical->toString();
    }
}
