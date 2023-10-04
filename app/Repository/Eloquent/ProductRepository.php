<?php

namespace App\Repository\Eloquent;

use App\Models\patient;
use App\Models\product;
use Illuminate\Database\Eloquent\Collection;
use App\Models\student;
use App\Repository\PatientRepositoryInterface;
use App\Repository\productRepositoryInterface;

class ProductRepository extends BaseRepository implements productRepositoryInterface
{

    public function __construct(product $model)
    {
        parent::__construct($model);
    }

    public function where($x,$y)
    {
        return product::where($x,$y)->first();
    }


    public function allProducts()
    {
        return product::all();
    }

}
