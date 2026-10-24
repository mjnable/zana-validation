<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * An absolute http or https URL.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Url extends Constraint
{
    public function validate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $valid = is_string($value)
            && filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);

        return $valid ? null : $this->message('This value is not a valid URL.');
    }
}
