<?php

namespace App\Security\Frontend;

use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface {

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username) {
        return new User($username, null, [ 'ROLE_USER']);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user) {
        return new User($user->getUsername(), null, [ 'ROLE_USER']);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class) {
        return $class === User::class;
    }
}