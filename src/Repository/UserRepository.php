<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function persist(User $user): void {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user): void {
        $this->em->remove($user);
        $this->em->flush();
    }

}