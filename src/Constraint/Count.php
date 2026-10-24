<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * The number of elements of an array or Countable.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Count extends Constraint
{
    public function __construct(
        public readonly ?int $min = null,
        public readonly ?int $max = null,
        ?string $message = null,
    ) {
        if ($min === null && $max === null) {
            throw new \InvalidArgumentException('Count needs a min, a max, or both.');
        }

        parent::__construct($message);
    }

    public function validate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value) && !$value instanceof \Countable) {
            return $this->message('This value should be a collection.');
        }

        $count = count($value);
        $parameters = ['min' => $this->min, 'max' => $this->max];

        if ($this->min !== null && $count < $this->min) {
            return $this->message('This collection should contain {min} elements or more.', $parameters);
        }

        if ($this->max !== null && $count > $this->max) {
            return $this->message('This collection should contain {max} elements or less.', $parameters);
        }

        return null;
    }
}
