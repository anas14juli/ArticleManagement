<?php

namespace App\Repository\Eloquent;

use App\Repository\StudentPersonalWorksRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\personal_work;


class StudentPersonalWorksRepository extends BaseRepository implements StudentPersonalWorksRepositoryInterface
{

    public function __construct(personal_work $model)
    {
        parent::__construct($model);
    }

    public function where2($x,$y,$z)
    {
        return personal_work::where($x,$y,$z)->first();
    }
    public function ShowMyWorks()
    {
        return personal_work::all();
    }

}
