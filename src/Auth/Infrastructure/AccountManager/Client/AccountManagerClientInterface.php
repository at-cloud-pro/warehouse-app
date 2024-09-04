<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\AccountManager\Client;

use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerUserDto;

interface AccountManagerClientInterface
{
    public function getUserByToken(Jwt $token): AccountManagerUserDto;
}
