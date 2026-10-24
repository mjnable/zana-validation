<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

/**
 * Shared message handling for the built-in constraints.
 *
 * @internal
 */
abstract class Constraint implements ConstraintInterface
{
    public function __construct(protected readonly ?string $message = null)
    {
    }

    /**
     * The custom message if one was given, otherwise the default, with {placeholders} replaced.
     *
     * @param array<string, scalar|null> $parameters
     */
    protected function message(string $default, array $parameters = []): string
    {
        $replacements = [];
        foreach ($parameters as $name => $value) {
            $replacements['{' . $name . '}'] = (string) $value;
        }

        return strtr($this->message ?? $default, $replacements);
    }
}
