<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * A number between min and max, inclusive.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Range extends Constraint
{
    public function __construct(
        public readonly int|float|null $min = null,
        public readonly int|float|null $max = null,
        ?string $message = null,
    ) {
        if ($min === null && $max === null) {
            throw new \InvalidArgumentException('Range needs a min, a max, or both.');
        }

        parent::__construct($message);
    }

    public function validate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_int($value) && !is_float($value)) {
            return $this->message('This value should be a number.');
        }

        $parameters = ['min' => $this->min, 'max' => $this->max];
        $tooLow = $this->min !== null && $value < $this->min;
        $tooHigh = $this->max !== null && $value > $this->max;

        return match (true) {
            !$tooLow && !$tooHigh => null,
            $this->min !== null && $this->max !== null => $this->message('This value should be between {min} and {max}.', $parameters),
            $tooLow => $this->message('This value should be {min} or more.', $parameters),
            default => $this->message('This value should be {max} or less.', $parameters),
        };
    }
}
