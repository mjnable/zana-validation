<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

/**
 * A rule checked against a property value.
 */
interface ConstraintInterface
{
    /**
     * The violation message, or null when the value is valid.
     */
    public function validate(mixed $value): ?string;
}
