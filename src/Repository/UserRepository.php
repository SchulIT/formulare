<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface {


    public function __construct(private readonly EntityManagerInterface $em) { }

    public function persist(User $user): void {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user): void {
        $this->em->remove($user);
        $this->em->flush();
    }

}