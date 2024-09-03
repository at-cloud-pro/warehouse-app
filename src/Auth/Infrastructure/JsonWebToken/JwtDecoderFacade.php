<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\JsonWebToken;

use App\Auth\Infrastructure\AccountManager\AccountManagerUser;

class JwtDecoderFacade
{
    /** @return mixed[] */
    public function decode(string $jwt): array
    {
        if (false === str_contains($jwt, '.')) {
            throw new \InvalidArgumentException('Invalid JWT');
        }

        [,$payload] = explode('.', $jwt);

        $decodedPayload = base64_decode($payload, true);

        if (false === $decodedPayload) {
            throw new \InvalidArgumentException('Invalid JWT payload');
        }

        return json_decode($decodedPayload, true);
    }

    public function getIdentifierFromToken(string $jwt): AccountManagerUser
    {
        $decoded = $this->decode($jwt);

        return new AccountManagerUser($decoded['uit'], $decoded['uiv']);
    }
}
