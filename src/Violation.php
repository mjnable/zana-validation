<?php

declare(strict_types=1);

namespace Zana\Validation;

/**
 * One failed constraint.
 */
final readonly class Violation
{
    /** The message before its {placeholders} were replaced; the message itself for custom constraints. */
    public string $template;

    /**
     * @param string $path Property path, e.g. "address.city" or "items[1].name"
     * @param string $constraint Short class name of the constraint, e.g. "NotBlank"
     * @param array<string, scalar|null> $parameters The values of the template's placeholders
     */
    public function __construct(
        public string $path,
        public string $message,
        public string $constraint,
        ?string $template = null,
        public array $parameters = [],
    ) {
        $this->template = $template ?? $message;
    }
}
