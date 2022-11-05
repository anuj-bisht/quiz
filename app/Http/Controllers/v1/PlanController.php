<?php

namespace App\Http\Controllers\v1;

use App\Plan;
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



class PlanController extends Controller
{
  
      
    /**
     * Edit event method
     * @return success or error
     * 
     * */
    public function getPlanList(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        //$user  = JWTAuth::user();
        $data = Plan::getAllList();
        
        if(!$data->count()){          
            return response()->json(['status'=>$status,'message'=>'No record found','data'=>$user]);                    
        }else{          
            return response()->json(['status'=>1,'message'=>'','data'=>$data]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
              
    }

    public function getPlanByCategory(Request $request){
      try{
        $status = 0;
        $message = "";
              
        $validator = Validator::make($request->all(), [
          'category_id' => 'required'                           
        ]);

        $params = [];  
        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->category_id[0])){
            $message = $error->category_id[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }

        //$user  = JWTAuth::user();
        $data = Plan::getPlanByCategory($request->category_id);
        // $planArr = [];
        // if($data->count()>0){
        //   foreach($data as $k=>$v){
        //     $planArr[$v->days][] = $v;
        //   }
        // }
        // $result = array_values($planArr);
        
        
        return response()->json(['status'=>1,'message'=>'','data'=>$data]);                    

      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }

}