<?php

declare(strict_types=1);

namespace Zana\Validation;

use Zana\Validation\Constraint\ConstraintInterface;
use Zana\Validation\Constraint\Valid;

/**
 * Validates objects against the constraint attributes on their properties.
 */
final class Validator
{
    /** @var array<class-string, list<array{\ReflectionProperty, list<ConstraintInterface>, bool}>> */
    private array $metadata = [];

    public function validate(object $object): ViolationList
    {
        $violations = [];
        $this->validateObject($object, '', $violations, []);

        return new ViolationList($violations);
    }

    /**
     * @throws ValidationException If the object has violations
     */
    public function assertValid(object $object): void
    {
        $violations = $this->validate($object);
        if ($violations->hasViolations()) {
            throw new ValidationException($violations);
        }
    }

    /**
     * @param list<Violation> $violations
     * @param array<int, true> $visiting Object ids on the current path, to stop on cycles
     */
    private function validateObject(object $object, string $prefix, array &$violations, array $visiting): void
    {
        $id = spl_object_id($object);
        if (isset($visiting[$id])) {
            return;
        }
        $visiting[$id] = true;

        foreach ($this->metadataFor($object) as [$property, $constraints, $cascade]) {
            $path = $prefix === '' ? $property->getName() : "{$prefix}.{$property->getName()}";
            $value = $property->isInitialized($object) ? $property->getValue($object) : null;

            foreach ($constraints as $constraint) {
                $message = $constraint->validate($value);
                if ($message !== null) {
                    $violations[] = new Violation($path, $message, substr((string) strrchr('\\' . $constraint::class, '\\'), 1));
                }
            }

            if ($cascade) {
                $this->cascade($value, $path, $violations, $visiting);
            }
        }
    }

    /**
     * @param list<Violation> $violations
     * @param array<int, true> $visiting
     */
    private function cascade(mixed $value, string $path, array &$violations, array $visiting): void
    {
        if (is_object($value) && !is_iterable($value)) {
            $this->validateObject($value, $path, $violations, $visiting);

            return;
        }

        if (is_iterable($value)) {
            foreach ($value as $key => $item) {
                if (is_object($item)) {
                    $this->validateObject($item, "{$path}[{$key}]", $violations, $visiting);
                }
            }
        }
    }

    /**
     * @return list<array{\ReflectionProperty, list<ConstraintInterface>, bool}>
     */
    private function metadataFor(object $object): array
    {
        return $this->metadata[$object::class] ??= $this->readMetadata(new \ReflectionClass($object));
    }

    /**
     * @param \ReflectionClass<object> $class
     * @return list<array{\ReflectionProperty, list<ConstraintInterface>, bool}>
     */
    private function readMetadata(\ReflectionClass $class): array
    {
        $metadata = [];
        foreach ($class->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $constraints = [];
            $cascade = false;
            foreach ($property->getAttributes(ConstraintInterface::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $constraint = $attribute->newInstance();
                if ($constraint instanceof Valid) {
                    $cascade = true;
                } else {
                    $constraints[] = $constraint;
                }
            }

            if ($constraints !== [] || $cascade) {
                $metadata[] = [$property, $constraints, $cascade];
            }
        }

        return $metadata;
    }
}
