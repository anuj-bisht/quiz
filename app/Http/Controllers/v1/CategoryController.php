<?php

namespace App\Http\Controllers\v1;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Traits\SendMail;
use Config;
use App\Common\Utility;
use App\Classes\UploadFile;
use Mail;


class CategoryController extends Controller
{
  
      
    /**
     * Edit event method
     * @return success or error
     * 
     * */
    public function getCategoryList(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        //$user  = JWTAuth::user();
        $data = Category::getAllList();
        
        if(!$data->count()){          
            return response()->json(['status'=>$status,'message'=>'No record found','data'=>$user]);                    
        }else{          
            return response()->json(['status'=>1,'message'=>'','data'=>$data]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
              
    }

}