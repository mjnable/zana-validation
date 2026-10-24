<?php

declare(strict_types=1);

namespace Zana\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Zana\Validation\ValidationException;
use Zana\Validation\Validator;
use Zana\Validation\Violation;

final class ValidatorTest extends TestCase
{
    public function testValidObjectsHaveNoViolations(): void
    {
        $patient = new RegisterPatient(
            'Ada Lovelace',
            'ada@example.org',
            165.0,
            'f',
            'https://example.org',
            new Address('London', '1234'),
            [new Item('scale')],
        );

        $this->assertFalse(new Validator()->validate($patient)->hasViolations());
        new Validator()->assertValid($patient);
    }

    public function testEveryViolationIsReportedWithItsPath(): void
    {
        $patient = new RegisterPatient(
            name: '',
            email: 'nope',
            heightCm: 20.0,
            address: new Address('', '12'),
            items: [new Item('ok'), new Item('far too long name', 0), new Item('x'), new Item('y')],
        );

        $violations = new Validator()->validate($patient);

        $this->assertSame([
            'name' => ['This value should not be blank.', 'This value is too short. It should have 2 characters or more.'],
            'email' => ['This value is not a valid email address.'],
            'heightCm' => ['This value should be between 50 and 250.'],
            'address.city' => ['This value should not be blank.'],
            'address.postcode' => ['Postcodes have four digits.'],
            'items' => ['This collection should contain 3 elements or less.'],
            'items[1].name' => ['This value is too long. It should have 10 characters or less.'],
            'items[1].quantity' => ['This value should be positive.'],
        ], $violations->toArray());

        $this->assertCount(9, $violations);
        $this->assertSame(['NotBlank', 'Length'], array_map(static fn (Violation $v): string => $v->constraint, $violations->forPath('name')));
    }

    public function testUninitialisedPropertiesCountAsNull(): void
    {
        $violations = new Validator()->validate(new Uninitialised());

        $this->assertSame(['required' => ['This value should not be null.']], $violations->toArray());
    }

    public function testCyclesAreNotFollowedTwice(): void
    {
        $a = new Node();
        $b = new Node();
        $a->next = $b;
        $b->next = $a;
        $b->label = '';

        $this->assertSame(['next.label' => ['This value should not be blank.']], new Validator()->validate($a)->toArray());
    }

    public function testStaticPropertiesAreIgnored(): void
    {
        $this->assertCount(0, new Validator()->validate(new WithStatic()));
    }

    public function testAssertValidThrowsWithTheViolations(): void
    {
        try {
            new Validator()->assertValid(new RegisterPatient('A'));
            $this->fail('assertValid() should throw.');
        } catch (ValidationException $e) {
            $this->assertSame('Validation failed with 1 violation. First: name: This value is too short. It should have 2 characters or more.', $e->getMessage());
            $this->assertCount(1, $e->violations);
        }
    }
}
