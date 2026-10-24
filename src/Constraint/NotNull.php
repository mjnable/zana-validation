<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class NotNull extends Constraint
{
    public function validate(mixed $value): ?string
    {
        return $value === null ? $this->message('This value should not be null.') : null;
    }
}
