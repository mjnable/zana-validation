<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Email extends Constraint
{
    public function validate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $valid = is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;

        return $valid ? null : $this->message('This value is not a valid email address.');
    }
}
