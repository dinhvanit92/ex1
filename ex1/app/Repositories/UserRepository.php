<?php


namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /** @var User */
    private $userModel;

    public function model(): string
    {
        return User::class;
    }


}
