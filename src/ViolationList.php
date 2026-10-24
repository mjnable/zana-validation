<?php

declare(strict_types=1);

namespace Zana\Validation;

/**
 * The violations found by one validation.
 *
 * @implements \IteratorAggregate<int, Violation>
 */
final readonly class ViolationList implements \IteratorAggregate, \Countable
{
    /**
     * @param list<Violation> $violations
     */
    public function __construct(private array $violations = [])
    {
    }

    public function hasViolations(): bool
    {
        return $this->violations !== [];
    }

    /**
     * @return list<Violation>
     */
    public function forPath(string $path): array
    {
        return array_values(array_filter($this->violations, static fn (Violation $v): bool => $v->path === $path));
    }

    /**
     * @return array<string, list<string>> path => messages
     */
    public function toArray(): array
    {
        $grouped = [];
        foreach ($this->violations as $violation) {
            $grouped[$violation->path][] = $violation->message;
        }

        return $grouped;
    }

    /**
     * @return \ArrayIterator<int, Violation>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->violations);
    }

    public function count(): int
    {
        return count($this->violations);
    }
}
