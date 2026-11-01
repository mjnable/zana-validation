<?php

declare(strict_types=1);

namespace Zana\Validation;

use Zana\Container\Contracts\ServiceCollectionInterface;
use Zana\Core\ServiceProvider;
use Zana\Http\ExceptionMapperInterface;
use Zana\Mediator\PipelineBehavior;

/**
 * Registers the Validator as a singleton. Needs mj-zana/core; the Validator
 * itself works without it. With mj-zana/mediator, also ValidationBehavior,
 * which applications list in mediator.behaviors; with mj-zana/http, the
 * mapper that turns a ValidationException into a 422 (ADR-025).
 */
final class ValidationServiceProvider extends ServiceProvider
{
    public function register(ServiceCollectionInterface $services): void
    {
        $services->singleton(Validator::class);

        if (interface_exists(PipelineBehavior::class)) {
            $services->singleton(ValidationBehavior::class);
        }
        if (interface_exists(ExceptionMapperInterface::class)) {
            $services->singleton(ValidationExceptionMapper::class)->tag(ExceptionMapperInterface::TAG, ValidationExceptionMapper::class);
        }
    }

    public function origin(): string
    {
        return 'package:mj-zana/validation';
    }
}
