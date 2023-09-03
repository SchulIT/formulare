<?php

namespace App\Security\Frontend;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface {

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface {
        return new User($user->getUserIdentifier(), null, [ 'ROLE_USER']);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class): bool {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface {
        return new User($identifier, null, [ 'ROLE_USER']);
    }
}