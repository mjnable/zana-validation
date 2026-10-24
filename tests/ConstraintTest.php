<?php

declare(strict_types=1);

namespace Zana\Validation\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Zana\Validation\Constraint\Choice;
use Zana\Validation\Constraint\ConstraintInterface;
use Zana\Validation\Constraint\Count;
use Zana\Validation\Constraint\Email;
use Zana\Validation\Constraint\Length;
use Zana\Validation\Constraint\NotBlank;
use Zana\Validation\Constraint\NotNull;
use Zana\Validation\Constraint\Positive;
use Zana\Validation\Constraint\Range;
use Zana\Validation\Constraint\Regex;
use Zana\Validation\Constraint\Url;

final class ConstraintTest extends TestCase
{
    /**
     * @return iterable<string, array{ConstraintInterface, mixed, ?string}>
     */
    public static function cases(): iterable
    {
        yield 'NotBlank passes' => [new NotBlank(), 'x', null];
        yield 'NotBlank null' => [new NotBlank(), null, 'This value should not be blank.'];
        yield 'NotBlank spaces' => [new NotBlank(), "  \t", 'This value should not be blank.'];
        yield 'NotBlank empty array' => [new NotBlank(), [], 'This value should not be blank.'];
        yield 'NotBlank zero is not blank' => [new NotBlank(), 0, null];
        yield 'NotNull passes' => [new NotNull(), '', null];
        yield 'NotNull fails' => [new NotNull(), null, 'This value should not be null.'];

        yield 'Length passes' => [new Length(2, 4), 'abc', null];
        yield 'Length counts characters' => [new Length(max: 3), 'éèê', null];
        yield 'Length too short' => [new Length(2, 4), 'a', 'This value is too short. It should have 2 characters or more.'];
        yield 'Length too long' => [new Length(2, 4), 'abcde', 'This value is too long. It should have 4 characters or less.'];
        yield 'Length exact' => [new Length(3, 3), 'ab', 'This value should have exactly 3 characters.'];
        yield 'Length not a string' => [new Length(1), 12, 'This value should be a string.'];
        yield 'Length null' => [new Length(1), null, null];

        yield 'Range passes' => [new Range(1, 10), 5.5, null];
        yield 'Range inclusive' => [new Range(1, 10), 10, null];
        yield 'Range between' => [new Range(1, 10), 11, 'This value should be between 1 and 10.'];
        yield 'Range min only' => [new Range(min: 18), 17, 'This value should be 18 or more.'];
        yield 'Range max only' => [new Range(max: 5), 6, 'This value should be 5 or less.'];
        yield 'Range not a number' => [new Range(1), '5', 'This value should be a number.'];

        yield 'Positive passes' => [new Positive(), 0.1, null];
        yield 'Positive zero' => [new Positive(), 0, 'This value should be positive.'];
        yield 'Positive not a number' => [new Positive(), 'x', 'This value should be a number.'];

        yield 'Email passes' => [new Email(), 'ada@example.org', null];
        yield 'Email fails' => [new Email(), 'ada@', 'This value is not a valid email address.'];
        yield 'Email not a string' => [new Email(), 5, 'This value is not a valid email address.'];

        yield 'Url https' => [new Url(), 'https://zana.dev/docs', null];
        yield 'Url ftp rejected' => [new Url(), 'ftp://zana.dev', 'This value is not a valid URL.'];
        yield 'Url relative rejected' => [new Url(), '/docs', 'This value is not a valid URL.'];

        yield 'Regex passes' => [new Regex('/^\d+$/'), '123', null];
        yield 'Regex fails' => [new Regex('/^\d+$/'), '12a', 'This value is not valid.'];

        yield 'Choice passes' => [new Choice(['a', 'b']), 'a', null];
        yield 'Choice is strict' => [new Choice([1, 2]), '1', 'The value you selected is not a valid choice.'];

        yield 'Count passes' => [new Count(1, 2), [1], null];
        yield 'Count too few' => [new Count(min: 2), [1], 'This collection should contain 2 elements or more.'];
        yield 'Count too many' => [new Count(max: 1), new \ArrayObject([1, 2]), 'This collection should contain 1 elements or less.'];
        yield 'Count not a collection' => [new Count(1), 'x', 'This value should be a collection.'];
    }

    #[DataProvider('cases')]
    public function testConstraint(ConstraintInterface $constraint, mixed $value, ?string $expected): void
    {
        $this->assertSame($expected, $constraint->validate($value));
    }

    public function testCustomMessagesReplacePlaceholders(): void
    {
        $this->assertSame('Between 1 and 9, please.', new Range(1, 9, message: 'Between {min} and {max}, please.')->validate(10));
        $this->assertSame('Pick one of f, m.', new Choice(['f', 'm'], message: 'Pick one of {choices}.')->validate('z'));
        $this->assertSame('Required!', new NotBlank(message: 'Required!')->validate(''));
    }

    public function testConstraintsWithoutLimitsAreRejected(): void
    {
        foreach ([static fn () => new Length(), static fn () => new Range(), static fn () => new Count(), static fn () => new Regex('/(/')] as $make) {
            try {
                $make();
                $this->fail('An invalid constraint should be rejected.');
            } catch (\InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
