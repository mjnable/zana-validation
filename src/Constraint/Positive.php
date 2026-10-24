<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Positive extends Constraint
{
    public function validate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_int($value) && !is_float($value)) {
            return $this->message('This value should be a number.');
        }

        return $value > 0 ? null : $this->message('This value should be positive.');
    }
}
