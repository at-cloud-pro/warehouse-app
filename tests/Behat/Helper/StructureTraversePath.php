<?php

declare(strict_types=1);

namespace App\Tests\Behat\Helper;

final class StructureTraversePath
{
    /** @var string[] */
    private array $elements;

    private function __construct(string ...$elements)
    {
        $this->elements = $elements;
    }

    public static function fromString(string $path, ?string $prefix = null): self
    {
        $trimmedPath = null === $prefix ? $path : self::trimPrefix($path, $prefix);

        if (!str_contains($trimmedPath, '.')) {
            return new self($trimmedPath);
        }

        $elements = explode('.', $trimmedPath);

        return new self(...$elements);
    }

    private static function trimPrefix(string $path, string $prefix): string
    {
        $prefixWithDot = "{$prefix}.";

        if (!str_starts_with($path, $prefixWithDot)) {
            return $path;
        }

        return substr($path, strlen($prefixWithDot));
    }

    /** @return string[] */
    public function getElements(): iterable
    {
        return $this->elements;
    }

    public function isProperty(): bool
    {
        return 1 === count($this->elements);
    }

    public function getByIndex(int $n): mixed
    {
        if ($n >= count($this->elements)) {
            throw new \RuntimeException("Path nth element does not exist (nth: {$n})");
        }

        return $this->elements[$n];
    }
}
