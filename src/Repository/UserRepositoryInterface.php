<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface {

    public function persist(User $user): void;

    public function remove(User $user): void;
}