<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * Validate the nested object, or each object of an array, with its own constraints.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Valid implements ConstraintInterface
{
    /**
     * Valid has no rule of its own; the Validator descends into the value.
     */
    public function validate(mixed $value): ?string
    {
        return null;
    }
}
