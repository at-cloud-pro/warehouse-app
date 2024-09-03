<?php

declare(strict_types=1);

namespace App\Tests\Behat\Helper;

enum Structure
{
    case ARRAY;

    case LIST;

    /** @param mixed[] $input */
    public static function fromArray(array $input): self
    {
        return array_is_list($input) ? self::LIST : self::ARRAY;
    }
}
