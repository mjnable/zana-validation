# Zana Validation

Attribute-based validation of objects, typically input DTOs. No dependencies.
Requires PHP 8.5 or later.

```
composer require mj-zana/validation
```

## Usage

```php
use Zana\Validation\Constraint\{Email, Length, NotBlank, Range, Valid};
use Zana\Validation\Validator;

final class RegisterPatient
{
    public function __construct(
        #[NotBlank] #[Length(min: 2, max: 50)] public string $name,
        #[Email] public ?string $email = null,
        #[Range(min: 50, max: 250)] public ?float $heightCm = null,
        #[Valid] public ?Address $address = null,
    ) {}
}

$violations = (new Validator())->validate($dto);
$violations->toArray();   // ['name' => ['This value should not be blank.'], 'address.city' => [...]]

(new Validator())->assertValid($dto);   // throws ValidationException
```

Every constraint on every property is checked, and all violations are reported.
Constraints other than `NotBlank` and `NotNull` pass on `null`.

## Constraints

`NotBlank`, `NotNull`, `Length(min, max)`, `Range(min, max)`, `Positive`, `Email`,
`Url` (http/https), `Regex(pattern)`, `Choice(choices)`, `Count(min, max)` and
`Valid` (validates nested objects, or each object of an array).

Each accepts a `message` with `{min}`, `{max}` or `{choices}` placeholders.
Custom constraints implement `ConstraintInterface` and are attributes.

## License

MIT — see [LICENSE](LICENSE).
