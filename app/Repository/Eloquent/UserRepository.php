<?php

namespace App\Repository\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use App\Repository\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    

}
