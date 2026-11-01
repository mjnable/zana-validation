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

## New in 2.1

Each `Violation` keeps its message `template` and `parameters`. Give the
`Validator` a `MessageTranslatorInterface` to translate them;
`mj-zana/translation` registers one, so custom messages can be keys:
`#[NotBlank(message: 'contact.error.name_required')]`.

## New in 2.10

- **`ValidationBehavior`** (with `mj-zana/mediator`): validates every command
  and query before its handler runs, whatever sends it. Opt-in:
  `return ['behaviors' => [ValidationBehavior::class]];` in
  `config/mediator.php`.
- **`ValidationExceptionMapper`** (with `mj-zana/http`): a `ValidationException`
  that reaches `ErrorMiddleware` is a 422 with `errors` per field.

The provider registers each only when its package is installed; the library
itself still needs nothing but PHP.

## License

MIT — see [LICENSE](LICENSE).
