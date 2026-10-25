<?php

declare(strict_types=1);

namespace Zana\Validation;

/**
 * Translates violation messages. The Validator passes the message template
 * (a custom message such as "contact.error.name_required", or the built-in
 * English text) and its parameters ({min}, {max}, ...). mj-zana/translation
 * provides an implementation.
 */
interface MessageTranslatorInterface
{
    /**
     * @param array<string, scalar|null> $parameters
     */
    public function translate(string $template, array $parameters): string;
}
