<?php

declare(strict_types=1);

namespace App\Tests\Behat\Helper;

class EntityClassMap
{
    /** @var array<string, class-string> */
    private static array $entities = [];

    /** @return class-string */
    public static function getByName(string $name): string
    {
        return self::$entities[$name];
    }
}
