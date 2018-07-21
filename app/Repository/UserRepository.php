<?php

namespace App\Repository;

use App\User;
use App\Repository\Contracts\UserRepository as UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @inheritdoc
     */ 
    public function getById(int $id) : ?User
    {
        return User::find($id);
    }
}
