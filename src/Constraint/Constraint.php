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
    /** @var array{string, array<string, scalar|null>}|null The template and parameters of the last message */
    private ?array $lastMessage = null;

    public function __construct(protected readonly ?string $message = null)
    {
    }

    /**
     * The template and parameters of the message the last validate() call
     * returned, for translation; cleared by reading it.
     *
     * @internal Used by the Validator.
     * @return array{string, array<string, scalar|null>}|null
     */
    public function takeLastMessage(): ?array
    {
        [$last, $this->lastMessage] = [$this->lastMessage, null];

        return $last;
    }

    /**
     * The custom message if one was given, otherwise the default, with {placeholders} replaced.
     *
     * @param array<string, scalar|null> $parameters
     */
    protected function message(string $default, array $parameters = []): string
    {
        $template = $this->message ?? $default;
        $this->lastMessage = [$template, $parameters];

        $replacements = [];
        foreach ($parameters as $name => $value) {
            $replacements['{' . $name . '}'] = (string) $value;
        }

        return strtr($template, $replacements);
    }
}
