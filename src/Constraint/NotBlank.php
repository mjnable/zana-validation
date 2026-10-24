<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * Rejects null, empty or whitespace-only strings, and empty arrays.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class NotBlank extends Constraint
{
    public function validate(mixed $value): ?string
    {
        $blank = $value === null
            || (is_string($value) && trim($value) === '')
            || $value === [];

        return $blank ? $this->message('This value should not be blank.') : null;
    }
}
