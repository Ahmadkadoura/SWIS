<?php

namespace App\Http\Repositories;

use App\Models\Ctn;

class ctnRepository extends baseRepository
{
    public function __construct(Ctn $model)
    {
        parent::__construct($model);
    }
    public function index():array
    {
        $data =Ctn::with('item')->paginate(10);
        if ($data->isEmpty()){
            $message="There are no ctn at the moment";
        }else
        {
            $message="ctn indexed successfully";
        }
        return ['message'=>$message,"ctn"=>$data];
    }

}
