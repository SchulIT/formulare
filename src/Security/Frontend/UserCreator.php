<?php

namespace App\Security\Frontend;

use App\Entity\User;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCreator implements UserCreatorInterface {

    /**
     * @inheritDoc
     */
    public function createUser(Response $response): ?UserInterface {
        return new User();
    }
}