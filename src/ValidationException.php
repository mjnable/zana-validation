<?php

declare(strict_types=1);

namespace Zana\Validation;

/**
 * Thrown by Validator::assertValid() when an object has violations.
 */
final class ValidationException extends \RuntimeException
{
    public function __construct(public readonly ViolationList $violations)
    {
        $first = iterator_to_array($violations)[0] ?? null;
        $summary = $first === null ? '' : " First: {$first->path}: {$first->message}";

        parent::__construct(sprintf('Validation failed with %d violation%s.%s', count($violations), count($violations) === 1 ? '' : 's', $summary));
    }
}
