<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends BaseController
{
   public function index(Request $request){
       $data = SubCategory::orderBy('id', 'DESC');
       if ($request->category_id ){
           $data =  $data->where('category_id',$request->category_id);
       }
     $data = $data->get();
       if(!count($data)){
           return $this->sendError(204,'No Data Found');
       }
       return $this->sendResponse('success',$data);
   }
}
