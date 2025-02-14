<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * @package RetailCrm\ServiceBundle\Tests\DataFixtures
 */
class User implements UserInterface
{
    public function getRoles(): array
    {
        return ["USER"];
    }

    public function getPassword(): string
    {
        return "123";
    }

    public function getSalt(): string
    {
        return "salt";
    }

    public function getUsername(): string
    {
        return "user";
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return "user";
    }
}
