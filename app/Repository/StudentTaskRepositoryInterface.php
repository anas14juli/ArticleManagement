<?php

namespace App\Repository;

interface StudentTaskRepositoryInterface extends EloquentRepositoryInterface
{
    public function showalltasks();
  

    public function FinishTask($model);
}