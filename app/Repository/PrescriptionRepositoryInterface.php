<?php

namespace App\Repository;

interface PrescriptionRepositoryInterface extends EloquentRepositoryInterface
{
    public function ShowAllPrescriptions();
  
}