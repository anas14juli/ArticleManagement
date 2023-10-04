<?php

namespace App\Repository\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use App\Models\student;
use App\Repository\StudentRepositoryInterface;

class studentRepository extends BaseRepository implements StudentRepositoryInterface
{

    public function __construct(student $model)
    {
        parent::__construct($model);
    }


    public function getAllStudents()
    {
        return student::all();
    }
    public function deleteStudent($model)
    {
        return $model->delete();
    }
}
