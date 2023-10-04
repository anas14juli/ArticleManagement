<?php

namespace App\Repository\Eloquent;

use App\Models\comment;
use App\Repository\CommentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{

    public function __construct(comment $model)
    {
        parent::__construct($model);
    }

    
}
