<?php

namespace HotelBundle\Service\Encryption;

class BCryptService implements EncryptionServiceInterface
{

    public function hash(string $password)
    {
        return password_hash($password, PASSWORD_BCRIPT);
    }

    public function verify(string $password, string $hash)
    {
        return password_verify($password, $hash);
    }
}