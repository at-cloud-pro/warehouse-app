<?php

declare(strict_types=1);

namespace App\Tests\Behat\Helper;

use App\AccountManager\Infrastructure\Doctrine\Entity\User\User;
use App\ServiceManager\Infrastructure\Doctrine\Entity\Profile;
use App\ServiceManager\Infrastructure\Doctrine\Entity\Service;
use App\ServiceManager\Infrastructure\Doctrine\Entity\ServiceBoard;
use App\ServiceManager\Infrastructure\Doctrine\Entity\ServiceEnvironment;
use App\ServiceManager\Infrastructure\Doctrine\Entity\ServiceRole;

class EntityFqcnMap
{
    /** @var array<string, class-string> */
    private static array $entities = [
        'Profile' => Profile::class,
        'User' => User::class,
        'Service' => Service::class,
        'ServiceEnvironment' => ServiceEnvironment::class,
        'ServiceBoard' => ServiceBoard::class,
        'ServiceRole' => ServiceRole::class,
    ];

    /** @return class-string */
    public static function getByName(string $name): string
    {
        return self::$entities[$name];
    }
}
