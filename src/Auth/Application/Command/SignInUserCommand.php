<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

use App\Auth\Domain\Jwt;

final readonly class SignInUserCommand
{
    public function __construct(public Jwt $token) {}
}
