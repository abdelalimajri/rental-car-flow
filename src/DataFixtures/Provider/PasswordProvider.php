<?php

namespace App\DataFixtures\Provider;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordProvider
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function encodePassword(string $plainPassword): string
    {
        $user = new User();

        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }

    public function hashPassword(string $plainPassword): string
    {
        return $this->encodePassword($plainPassword);
    }
}
