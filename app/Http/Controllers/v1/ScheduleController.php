<?php

namespace App\Http\Controllers\v1;

use App\Schedule;
use App\Demorequest;
use App\Testrequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Traits\SendMail;
use App\Http\Controllers\Traits\Common;
use Config;
use App\Common\Utility;
use App\Classes\UploadFile;
use Mail;



class ScheduleController extends Controller
{
    use SendMail,Common;
      
    /**
     * Edit event method
     * @return success or error
     * 
     * */
    public function getTrainerScheduleByCategory(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        $user  = JWTAuth::user();
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

        $params['category_id'] = $request->category_id;
        
        $data = Schedule::getTrainerScheduleByCategory($params);
        
        if(!$data->count()){          
            return response()->json(['status'=>$status,'message'=>'No record found','data'=>json_decode("{}")]);                    
        }else{          
            return response()->json(['status'=>1,'message'=>'','data'=>$data]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }


    public function getScheduleByCategory(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        $user  = JWTAuth::user();
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

        $params['category_id'] = $request->category_id;
        
        $data = Schedule::getScheduleByCategory($params);
        
        if(!$data->count()){          
            return response()->json(['status'=>$status,'message'=>'No record found','data'=>json_decode("{}")]);                    
        }else{          
            return response()->json(['status'=>1,'message'=>'','data'=>$data]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }
    
    public function getAllSchedule(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        $user  = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>$status,'message'=>'User not found','data'=>json_decode("{}")]);                              
        }
        $data = Schedule::getAllSchedule($user->id);
        
        if(!$data->count()){          
            return response()->json(['status'=>$status,'message'=>'No record found','data'=>json_decode("{}")]);                    
        }else{          
            return response()->json(['status'=>1,'message'=>'','data'=>$data]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }

    public function getMyScheduleToday(Request $request){
      
      try{
        $status = 0;
        $message = "";
        $category_id = 0;      
        $user  = JWTAuth::user();

        if(isset($request->category_id)){
          $category_id = $request->category_id;
        }

        if(isset($request->date)){
          $date = $request->date;
        }else{
          $date = date('Y-m-d');
        }
        
        $data = Schedule::getMyScheduleToday($user->id,$category_id);
        $demo = Demorequest::getClientDemoRequestDateWise($user->id,$category_id,$date);
        $test = Testrequest::getClientTestRequestDateWise($user->id,$category_id,$date);
        return response()->json(['status'=>1,'message'=>'','data'=>['schedule'=>$data,'demo'=>$demo,'test'=>$test]]); 
        
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }

    public function rescheduleRequest(Request $request){
      
      try{
        $status = 0;
        $message = "";
        $category_id = 0;      
        $user  = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>$status,'message'=>'User not found','data'=>json_decode("{}")]);   
        }
        $validator = Validator::make($request->all(), [
          'id' => 'required'
        ]);
        $params = [];  
        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->id[0])){
            $message = $error->id[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }

        $chkS = Schedule::checkRescheduleRequest($user->id);
        
        if($chkS->count() > 1 ){
          return response()->json(['status'=>0,'message'=>'You can not reschedule more than 2 in one month','data'=>json_decode("{}")]);             
        }

        $scheduleData = Schedule::getScheduleById($request->id);
        if(!isset($scheduleData->id)){
          return response()->json(['status'=>0,'message'=>'No record found for reschedule','data'=>json_decode("{}")]);   
        }

        if($scheduleData->reschedule_status == 'Y'){
          return response()->json(['status'=>0,'message'=>'Your request is already submitted','data'=>json_decode("{}")]);   
        }

        if($this->datecompare($scheduleData->start,date('Y-m-d H:i:s'))){
          $diffArr = $this->datediffs($scheduleData->start,date('Y-m-d H:i:s'));
          if($diffArr <= 2){
            return response()->json(['status'=>$status,'message'=>'You can not reschedule call from now','data'=>json_decode("{}")]);  
          }
          $obj = Schedule::findOrFail($scheduleData->id);
          $obj->reschedule_status = 'Y';
          if($obj->save()){

            $data = [];
            $data['to_email'] = config('app.from_email');      
            $data['from'] = config('app.MAIL_FROM_ADDRESS');                  
            $data['subject'] = 'New Reschedule request';                
            $data['message1'] = 'New reschedule request is added by user, please take a action asap.';
            $data['name'] = 'Admin';
            
            $data['category_name'] = $scheduleData->category_name;
            $data['start_time'] = $scheduleData->start;
            $data['name1'] = $scheduleData->username;
            $data['username'] = $scheduleData->username;
            $data['email'] = $scheduleData->user_email;                
            $this->SendMail($data,'reschedule'); 
            
            $data['name'] = $scheduleData->username;
            $data['message1'] = 'You reschedule request is captured with our system, Admin will take an action and will notify you soon.';
            $data['to_email'] = $scheduleData->user_email;   
            if(isset($scheduleData->trainer_email)){
              $data['bcc'] = $scheduleData->trainer_email;
            }   
            $this->SendMail($data,'reschedule'); 

            if(isset($scheduleData->device_id)){
              $diviceIds = [$scheduleData->device_id];
              $this->sendNotification($diviceIds,'','Reschedule request success','Your reschedule request successfully sentuccessfully sent, Our team will keep you posted with updates');
            }            
            return response()->json(['status'=>1,'message'=>'Your request is registred successfully','data'=>json_decode("{}")]);  
          }else{
            return response()->json(['status'=>$status,'message'=>'error','data'=>json_decode("{}")]);  
          }
        }else{
          return response()->json(['status'=>$status,'message'=>'Requested schedule is old one','data'=>json_decode("{}")]);
        }

      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }


    

}