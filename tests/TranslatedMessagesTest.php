<?php

declare(strict_types=1);

namespace Zana\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Zana\Validation\Constraint\ConstraintInterface;
use Zana\Validation\Constraint\Length;
use Zana\Validation\Constraint\NotBlank;
use Zana\Validation\MessageTranslatorInterface;
use Zana\Validation\Validator;

final class TranslatedMessagesTest extends TestCase
{
    public function testViolationsKeepTheirTemplateAndParameters(): void
    {
        $violations = (new Validator())->validate(new ContactForm('', 'Hi'))->getIterator()->getArrayCopy();

        $this->assertSame('contact.error.name_required', $violations[0]->template);
        $this->assertSame('contact.error.name_required', $violations[0]->message, 'untranslated, as before');
        $this->assertSame('This value is too short. It should have {min} characters or more.', $violations[1]->template);
        $this->assertSame(['min' => 10, 'max' => null], $violations[1]->parameters);
        $this->assertSame('This value is too short. It should have 10 characters or more.', $violations[1]->message);
    }

    public function testATranslatorReceivesTemplatesAndParameters(): void
    {
        $translator = new class () implements MessageTranslatorInterface {
            public function translate(string $template, array $parameters): string
            {
                return match ($template) {
                    'contact.error.name_required' => 'Le nom est obligatoire.',
                    default => "fr({$template}|" . json_encode($parameters) . ')',
                };
            }
        };

        $violations = (new Validator($translator))->validate(new ContactForm('', 'Hi'))->getIterator()->getArrayCopy();

        $this->assertSame('Le nom est obligatoire.', $violations[0]->message);
        $this->assertSame('fr(This value is too short. It should have {min} characters or more.|{"min":10,"max":null})', $violations[1]->message);
    }

    public function testCustomConstraintsUseTheirMessageAsTemplate(): void
    {
        $violations = (new Validator())->validate(new Shout('quiet'))->getIterator()->getArrayCopy();

        $this->assertSame('Say it louder.', $violations[0]->template);
        $this->assertSame([], $violations[0]->parameters);
    }
}

final readonly class ContactForm
{
    public function __construct(
        #[NotBlank(message: 'contact.error.name_required')]
        public string $name,
        #[Length(min: 10)]
        public string $message,
    ) {
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
final class Loud implements ConstraintInterface
{
    public function validate(mixed $value): ?string
    {
        return is_string($value) && strtoupper($value) === $value ? null : 'Say it louder.';
    }
}

final readonly class Shout
{
    public function __construct(#[Loud] public string $text)
    {
    }
}
