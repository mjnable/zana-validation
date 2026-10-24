<?php

declare(strict_types=1);

namespace Zana\Validation;

/**
 * One failed constraint.
 */
final readonly class Violation
{
    /**
     * @param string $path Property path, e.g. "address.city" or "items[1].name"
     * @param string $constraint Short class name of the constraint, e.g. "NotBlank"
     */
    public function __construct(
        public string $path,
        public string $message,
        public string $constraint,
    ) {
    }
}
