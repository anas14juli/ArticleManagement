<?php

namespace App\Repository;

interface StudentRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAllStudents();
    public function deleteStudent($model);
}
