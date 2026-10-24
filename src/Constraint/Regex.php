<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * The string must match the pattern.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class Regex extends Constraint
{
    public function __construct(public readonly string $pattern, ?string $message = null)
    {
        if (@preg_match($pattern, '') === false) {
            throw new \InvalidArgumentException("Invalid regular expression: {$pattern}");
        }

        parent::__construct($message);
    }

    public function validate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $valid = is_string($value) && preg_match($this->pattern, $value) === 1;

        return $valid ? null : $this->message('This value is not valid.', ['pattern' => $this->pattern]);
    }
}
