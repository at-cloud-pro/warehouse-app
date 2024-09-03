<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NullTest extends WebTestCase
{
    public function testNull(): void
    {
        self::assertTrue(true);
    }
}
