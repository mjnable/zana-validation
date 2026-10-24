<?php

declare(strict_types=1);

namespace Zana\Validation\Tests;

use Zana\Validation\Constraint\Choice;
use Zana\Validation\Constraint\Count;
use Zana\Validation\Constraint\Email;
use Zana\Validation\Constraint\Length;
use Zana\Validation\Constraint\NotBlank;
use Zana\Validation\Constraint\NotNull;
use Zana\Validation\Constraint\Positive;
use Zana\Validation\Constraint\Range;
use Zana\Validation\Constraint\Regex;
use Zana\Validation\Constraint\Url;
use Zana\Validation\Constraint\Valid;

final class Address
{
    public function __construct(
        #[NotBlank] public string $city = '',
        #[Regex('/^\d{4}$/', message: 'Postcodes have four digits.')] public ?string $postcode = null,
    ) {
    }
}

final class Item
{
    public function __construct(#[NotBlank] #[Length(max: 10)] public string $name, #[Positive] public int $quantity = 1)
    {
    }
}

final class RegisterPatient
{
    /**
     * @param list<Item> $items
     */
    public function __construct(
        #[NotBlank] #[Length(min: 2, max: 50)] public string $name,
        #[Email] public ?string $email = null,
        #[Range(min: 50, max: 250)] public ?float $heightCm = null,
        #[Choice(['f', 'm', 'x'])] public ?string $sex = null,
        #[Url] public ?string $website = null,
        #[Valid] public ?Address $address = null,
        #[Valid] #[Count(max: 3)] public array $items = [],
    ) {
    }
}

final class Uninitialised
{
    #[NotNull]
    public string $required;
}

final class Node
{
    #[Valid]
    public ?Node $next = null;

    #[NotBlank]
    public string $label = 'node';
}

final class WithStatic
{
    #[NotBlank]
    public static string $ignored = '';
}
