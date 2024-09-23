<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\Doctrine\Fixture;

use App\Auth\Infrastructure\Doctrine\Fixtures\AppFixtures;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

final class AppFixturesTest extends TestCase
{
    public function testNoFixturesLoaded(): void
    {
        $manager = $this->createMock(ObjectManager::class);
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->once())->method('flush');

        $fixtures = new AppFixtures();
        $fixtures->load($manager);
    }
}
