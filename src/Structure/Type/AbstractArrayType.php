<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use Countable;

use function count;
use function is_array;

/**
 * @template TValues
 */
abstract readonly class AbstractArrayType implements TypeInterface
{
    protected int $length;

    /**
     * @param TValues $values
     */
    public function __construct(protected iterable|null $values, ?int $length = null)
    {
        $this->length = $length ?? $this->calculateLength();
    }

    /**
     * @return TValues
     */
    public function getValue(): ?iterable
    {
        return $this->values;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @inheritDoc
     *
     * Equality checks on array-likes are hard! There's absolutely no way we cann do it with Generators. With lists we
     * should be checking that the order matches and the values are loosely equal. But that will involve coercing a
     * `Traverable` into something comparable. The only place this is used (so far) is validating inputs for `SetType`.
     * I have no idea whether sets of lists or maps or sets are common in the wild, so I'm leaving it for another day.
     *
     * But yeah, if you're here, this is pants.
     */
    public function equals(mixed $other): bool
    {
        if (! $this->isSimilar($other)) {
            return false;
        }

        if ($this->values === null || $other->values === null) {
            return $this->values === $other->values;
        }

        // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
        return $other->values == $this->values;
    }

    /**
     * @psalm-assert-if-true AbstractArrayType $other
     */
    protected function isSimilar(mixed $other): bool
    {
        if (! $other instanceof $this) {
            return false;
        }

        if ($other->length !== $this->length) {
            return false;
        }

        return true;
    }

    protected function calculateLength(): int
    {
        return match (true) {
            $this->values === null             => 0,
            is_array($this->values)            => count($this->values),
            $this->values instanceof Countable => $this->values->count(),
            default                            => throw TypeException::unknownLength($this, $this->values),
        };
    }
}
