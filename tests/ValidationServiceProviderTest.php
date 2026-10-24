<?php

declare(strict_types=1);

namespace Zana\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Zana\Core\Application;
use Zana\Validation\ValidationServiceProvider;
use Zana\Validation\Validator;

final class ValidationServiceProviderTest extends TestCase
{
    public function testTheValidatorIsASingleton(): void
    {
        $container = Application::configure(sys_get_temp_dir() . '/zana-validation-app')
            ->withProviders(ValidationServiceProvider::class)
            ->build()
            ->container();

        $validator = $container->get(Validator::class);
        $this->assertInstanceOf(Validator::class, $validator);
        $this->assertSame($validator, $container->createScope()->get(Validator::class));
    }
}
