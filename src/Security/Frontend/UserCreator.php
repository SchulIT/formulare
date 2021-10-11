<?php

namespace App\Security\Frontend;

use App\Entity\User;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;

class UserCreator implements UserCreatorInterface {

    /**
     * @inheritDoc
     */
    public function createUser(Response $response) {
        return new User();
    }
}