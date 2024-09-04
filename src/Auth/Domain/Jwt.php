<?php

declare(strict_types=1);

namespace App\Auth\Domain;

final readonly class Jwt
{
    public function __construct(private string $value) {}

    /** @return mixed[] */
    public function decode(): array
    {
        if (false === str_contains($this->value, '.')) {
            throw new \InvalidArgumentException('Invalid JWT');
        }

        [,$payload] = explode('.', $this->value);

        $decodedPayload = base64_decode($payload, true);

        if (false === $decodedPayload) {
            throw new \InvalidArgumentException('Invalid JWT payload');
        }

        return json_decode($decodedPayload, true);
    }

    public function getUser(): Account
    {
        $decoded = $this->decode();
        $identifier = new AccountIdentifier($decoded['uit'], $decoded['uiv']);

        return new Account($identifier, $this);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
