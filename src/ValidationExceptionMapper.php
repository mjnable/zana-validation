<?php

declare(strict_types=1);

namespace Zana\Validation;

use Zana\Http\Exception\HttpException;
use Zana\Http\Exception\UnprocessableEntityHttpException;
use Zana\Http\ExceptionMapperInterface;

/**
 * A ValidationException that reaches HTTP is a 422 with "errors":
 * {path: [messages]} (ADR-025). Needs mj-zana/http;
 * ValidationServiceProvider registers it only then.
 */
final readonly class ValidationExceptionMapper implements ExceptionMapperInterface
{
    public function map(\Throwable $exception): ?HttpException
    {
        return $exception instanceof ValidationException
            ? new UnprocessableEntityHttpException($exception->violations->toArray(), previous: $exception)
            : null;
    }
}
