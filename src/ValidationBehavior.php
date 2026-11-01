<?php

declare(strict_types=1);

namespace Zana\Validation;

use Zana\Mediator\PipelineBehavior;

/**
 * Validates every command and query against its constraint attributes
 * before its handler runs, whatever sent it: an API action, a console
 * command, a queue job or another handler (FE-53, ADR-025). Enable it in
 * config/mediator.php:
 *
 *     return ['behaviors' => [ValidationBehavior::class]];
 *
 * Needs mj-zana/mediator; ValidationServiceProvider registers it only then.
 * Over HTTP, the ValidationException it throws becomes a 422 with the field
 * errors (ValidationExceptionMapper).
 */
final readonly class ValidationBehavior implements PipelineBehavior
{
    public function __construct(private Validator $validator)
    {
    }

    /**
     * @throws ValidationException
     */
    public function handle(object $message, \Closure $next): mixed
    {
        $this->validator->assertValid($message);

        return $next($message);
    }
}
