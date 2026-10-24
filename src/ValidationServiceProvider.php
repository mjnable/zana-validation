<?php

declare(strict_types=1);

namespace Zana\Validation;

use Zana\Container\Contracts\ServiceCollectionInterface;
use Zana\Core\ServiceProvider;

/**
 * Registers the Validator as a singleton. Needs mj-zana/core; the Validator
 * itself works without it.
 */
final class ValidationServiceProvider extends ServiceProvider
{
    public function register(ServiceCollectionInterface $services): void
    {
        $services->singleton(Validator::class);
    }

    public function origin(): string
    {
        return 'package:mj-zana/validation';
    }
}
