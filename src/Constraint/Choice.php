<?php

declare(strict_types=1);

namespace Zana\Validation\Constraint;

use Attribute;

/**
 * The value must be one of the choices (strict comparison).
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Choice extends Constraint
{
    /**
     * @param list<scalar> $choices
     */
    public function __construct(public readonly array $choices, ?string $message = null)
    {
        parent::__construct($message);
    }

    public function validate(mixed $value): ?string
    {
        if ($value === null || in_array($value, $this->choices, true)) {
            return null;
        }

        return $this->message(
            'The value you selected is not a valid choice.',
            ['choices' => implode(', ', array_map(static fn (mixed $c): string => (string) $c, $this->choices))]
        );
    }
}
