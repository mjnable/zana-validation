<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * String length in characters (multibyte-aware).
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Length extends Constraint
{
    public function __construct(
        public readonly ?int $min = null,
        public readonly ?int $max = null,
        ?string $message = null,
    ) {
        if ($min === null && $max === null) {
            throw new \InvalidArgumentException('Length needs a min, a max, or both.');
        }

        parent::__construct($message);
    }

    public function validate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            return $this->message('This value should be a string.');
        }

        $length = mb_strlen($value);

        if ($this->min !== null && $this->min === $this->max && $length !== $this->min) {
            return $this->message('This value should have exactly {min} characters.', ['min' => $this->min, 'max' => $this->max]);
        }

        if ($this->min !== null && $length < $this->min) {
            return $this->message('This value is too short. It should have {min} characters or more.', ['min' => $this->min, 'max' => $this->max]);
        }

        if ($this->max !== null && $length > $this->max) {
            return $this->message('This value is too long. It should have {max} characters or less.', ['min' => $this->min, 'max' => $this->max]);
        }

        return null;
    }
}
