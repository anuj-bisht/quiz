<?php

namespace App\Http\Controllers\v1;

use App\User;
use App\Category;
use App\Review;
use App\Notification;
use App\Contactus;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
use GuzzleHttp\Client;
use Mail;
use App\Mail\RegistrationMail;
use Stichoza\GoogleTranslate\GoogleTranslate;


class UserController extends Controller
{
  use SendMail;
  use Common;

  public function sendotp(Request $request){
    
    try{
      $status = 0;
      $message = "";
                  
            
      $validator = Validator::make($request->all(), [          
        'phone' => 'required|string|max:10|min:10'      
      ]);

      
      if($validator->fails()){
         $error = json_decode(json_encode($validator->errors()));
         if(isset($error->phone[0])){
           $message = $error->phone[0];
         }

         return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>$message,"data"=>json_decode("{}")]);
       }
      

      $userList = User::where('phone',$request->phone)->first();
      
      $otp = rand(100000,999999);
      //$otp = 123456;
      if($userList !=null && $userList->count() > 0){
	
        $phone = $request->phone;
        $message = $otp.' is your OTP to register/access Test Time (T2) App. It will be valid for 3 minutes.-Squaricle';

            $client = new Client();
            $url = "http://sendsms.designhost.in/index.php/smsapi/httpapi/";
            $response = $client->put($url,[
               'headers' => ['Content-type' => 'application/json'],
               'json' => ['uname' => 'squaricle',
               'password' => '123456',
			         'sender' => 'SQUATP',
               'tempid' => '1607100000000152634',
               'receiver' => $phone,
			         'route' => 'TA',
			         'msgtype' => '1',
               'sms' => $message,
               'format' => 'json'
                   ],
           ]);
            if ($response->getStatusCode() == 200) { // 200 OK
                    $response_data = $response->getBody()->getContents();
            }

        $userList->otp = $otp;
        $userList->otp_expiration_time = time();
        $userList->save();
        if($this->SendSms($phone,$message)){
          
          return response()->json(["status"=>1,"responseCode"=>"APP001","message"=>"OTP Sent","otp"=>$otp,"data"=>json_decode("{}")]);
        }
                
      }else{       
          return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'User not fount. please register first','data'=>json_decode("{}")]);       
      }            
         
    }catch(Exception $e){
      return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'User update Error','data'=>json_decode("{}")]);                    
    }
            
  }
  public function Resendotp(Request $request){
    
    try{
      $status = 0;
      $message = "";
                  
            
      $validator = Validator::make($request->all(), [          
        'phone' => 'required|string|max:10|min:10'      
      ]);

      
      if($validator->fails()){
         $error = json_decode(json_encode($validator->errors()));
         if(isset($error->phone[0])){
           $message = $error->phone[0];
         }

         return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>$message,"data"=>json_decode("{}")]);
       }
      

      $userList = User::where('phone',$request->phone)->first();
      
      $otp = rand(100000,999999);
      //$otp = 123456;
      if($userList !=null && $userList->count() > 0){
	
        $phone = $request->phone;
        $message = $otp.' is your OTP to register/access Test Time (T2) App. It will be valid for 3 minutes.-Squaricle';

            $client = new Client();
            $url = "http://sendsms.designhost.in/index.php/smsapi/httpapi/";
            $response = $client->put($url,[
               'headers' => ['Content-type' => 'application/json'],
               'json' => ['uname' => 'squaricle',
               'password' => '123456',
			         'sender' => 'SQUATP',
               'tempid' => '1607100000000152634',
               'receiver' => $phone,
			         'route' => 'TA',
			         'msgtype' => '1',
               'sms' => $message,
               'format' => 'json'
                   ],
           ]);
            if ($response->getStatusCode() == 200) { // 200 OK
                    $response_data = $response->getBody()->getContents();
            }

        $userList->otp = $otp;
        $userList->otp_expiration_time = time();
        $userList->save();
        if($this->SendSms($phone,$message)){
          return response()->json(["status"=>1,"responseCode"=>"APP001","message"=>"OTP Sent","otp"=>$otp,"data"=>json_decode("{}")]);
        }
                
      }else{       
          return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'User not fount. please register first','data'=>json_decode("{}")]);       
      }            
         
    }catch(Exception $e){
      return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'User update Error','data'=>json_decode("{}")]);                    
    }
            
  }

  public function authenticate(Request $request)
  {
      
      $status = 0;
      $message = "";

                    
      $validator = Validator::make($request->all(), [            
          'phone' => 'required|string|max:10',
          'otp' => 'required|string',
      ]);        
      //$validator->errors()
      if($validator->fails()){
        return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"invalid input details","data"=>json_decode("{}")]);
      }
      //echo $pwd = Hash::make($request->password).'      ='.$request->email; die;
      $validationChk = User::where('phone',$request->phone)->get();
      
      
      if($validationChk->count()==0){
        return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"invalid credentials","data"=>json_decode("{}")]);          
      }else if($validationChk[0]->status != '1'){
        return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"User not verified","data"=>json_decode("{}")]);          
      }
      
      // $otp_time_stamp = (int) $validationChk[0]->otp_expiration_time; 
      // $curr_time_stamp = time(); 
      // $diff = $curr_time_stamp - $otp_time_stamp; 
      // $minute = ($diff / 60); 
      // if($minute > 3){ 
      //   return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"Otp is expired","data"=>json_decode("{}")]);   
      // }

      $credentials = $request->only('phone', 'otp');                

      
      try {
        $myTTL = 43200; //minutes
        JWTAuth::factory()->setTTL($myTTL);            
          if (! $token = JWTAuth::attempt($credentials, ['status'=>'1'])) {            
              $message = 'invalid_credentials';                
              return response()->json(['status'=>$status,"responseCode"=>"NP997",'message'=>$message,'data'=>json_decode("{}")]);
          }
      } catch (JWTException $e) {
          $message = 'could_not_create_token';
          return response()->json(['status'=>$status,"responseCode"=>"NP997",'message'=>$message,'data'=>json_decode("{}")]);            
      }        
      $user  = JWTAuth::user();


      unset($user->otp);
      unset($user->verified_otp);
      $user->token = $token;
      $user->remember_token = $token;
      $user->device_id = (isset($request->device_id)) ? $request->device_id : '';
      //$this->SendSms($request->phone,'Welcome to LivFit Your OTP is: '.$otp);
      $user->save();
      unset($user->remember_token);
      $status = 1;     
      
      if(isset($user->userProfile)){
        foreach($user->userProfile as $k=>$v){
          if($v->default=='Y'){
            
            $user->language_id = $v->language_id;
            $user->district_id = $v->district_id;
            $user->class_id = $v->class_id;
            $user->school_id = $v->school_id;            
          }
        }
      }

      return response()->json(['status'=>$status,"responseCode"=>"APP001",'message'=>$message,'data'=>$user]);
  }


  public function socialcheck(Request $request)
  {
      
      $status = 0;
      $message = "";

                    
      $validator = Validator::make($request->all(), [            
          'social_id' => 'required',
          'name' => 'required|string',          
      ]);        
      //$validator->errors()
      if($validator->fails()){
        return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"invalid input details","data"=>json_decode("{}")]);
      }
      //echo $pwd = Hash::make($request->password).'      ='.$request->email; die;
      $validationChk = User::where('social_id',$request->social_id)->get();
      $obj = new User();
      if($validationChk->count()>0){

        $obj = $validationChk[0];
        $otp = rand(100000,999999);
        $obj->otp = $otp;
        $obj->save();

        $credentials = ['phone'=>$obj->phone,'otp'=>$obj->otp];                
      
        try {
          $myTTL = 43200; //minutes
          JWTAuth::factory()->setTTL($myTTL);            
            if (! $token = JWTAuth::attempt($credentials, ['status'=>'1'])) {            
                $message = 'invalid_credentials';                
                return response()->json(['status'=>$status,"responseCode"=>"NP997",'message'=>$message,'data'=>json_decode("{}")]);
            }
        } catch (JWTException $e) {
            $message = 'could_not_create_token';
            return response()->json(['status'=>$status,"responseCode"=>"NP997",'message'=>$message,'data'=>json_decode("{}")]);            
        }
        
        $user  = JWTAuth::user();
        unset($user->otp);
        unset($user->verified_otp);
        $user->token = $token;
        $user->remember_token = $token;
        $user->device_id = (isset($request->device_id)) ? $request->device_id : '';

        //$this->SendSms($request->phone,'Welcome to LivFit Your OTP is: '.$otp);
        $user->save();
        unset($user->remember_token);
        $status = 1;        
        return response()->json(['status'=>$status,"responseCode"=>"0001",'message'=>$message,'data'=>$user]);
      }else{               
        return response()->json(['status'=>1,"responseCode"=>"0000",'message'=>'You are not associted with us, Please register first','data'=>json_decode("{}")]);            
      }
                   
  }

  public function loginSocial(Request $request)
  {
	$message = "";$status = 0;
  try{
	$credentials = $request->only('phone', 'otp', 'email');
	$myTTL = 43200; //minutes
        JWTAuth::factory()->setTTL($myTTL);            
          if (! $token = JWTAuth::attempt($credentials, ['status'=>'1'])) {            
              $message = 'invalid_credentials';                
              return response()->json(['status'=>$status,"responseCode"=>"NP997",'message'=>$message,'data'=>json_decode("{}")]);
	}	
	$user  = JWTAuth::user();
	unset($user->otp);
      	unset($user->verified_otp);
      	$user->token = $token;
      	$user->remember_token = $token;
	
      	$user->save();
      	$status = 1; 
	//dd($request->email,$token,$user->token,$user);       
    if($request->social_id != "" && ($request->type != "normal" || $request->type != "NORMAL")){
      if($request->email != ""){
        $userList = User::where('email',$request->email)->first();
        if($userList != ""){
          $userProfile = DB::table('user_profiles')->where('user_id', $userList->id)->first();
          User::where('email',$request->email)->update(['device_token' => $request->device_token]);
          if($userProfile)
          {
            $userList['school_id'] =  $userProfile->school_id;
            $userList['class_id'] =  $userProfile->class_id;
            $userList['district_id'] =  $userProfile->district_id;
            $userList['language_id'] =  $userProfile->language_id;
	    $userList['roll_no'] =  $userProfile->roll_no;
            $userList['token'] =  $userList->token;
          }
          return response()->json(['status'=>2,'message'=>'Successful Login','data'=>$userList]);
        }else{
          return response()->json(['status'=>0,'message'=>'Register First','data'=>json_decode("{}")]);
        }
      }elseif($request->social_id != ""){
        $userList = User::where('social_id',$request->social_id)->first();
        if($userList != ""){
          $userProfile = DB::table('user_profiles')->where('user_id', $userList->id)->first();
          User::where('social_id',$request->social_id)->update(['device_token' => $request->device_token]);
          if($userProfile)
          {
            $userList['school_id'] =  $userProfile->school_id;
            $userList['class_id'] =  $userProfile->class_id;
            $userList['district_id'] =  $userProfile->district_id;
            $userList['language_id'] =  $userProfile->language_id;
		$userList['roll_no'] =  $userProfile->roll_no;
            $userList['token'] =  $userList->token;
          }
          return response()->json(['status'=>2,'message'=>'Successful Login','data'=>$userList]);
        }else{
          return response()->json(['status'=>0,'message'=>'Register First','data'=>json_decode("{}")]);
        }
      }else{
        return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'Not able to    login','data'=>json_decode("{}")]);
      }
    }else{
      return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'Social_id and Type is Required','data'=>json_decode("{}")]);
    }
      }catch(Exception $e){
          return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'Not able to    login','data'=>json_decode("{}")]);
      }
  }

public function loginNormal(Request $request)
  {
$status = 0;
      $message = "";

                    
      $validator = Validator::make($request->all(), [            
          'phone' => 'required|string|max:10',
          'otp' => 'required|string',
      ]); 
      //$validator->errors()
      if($validator->fails()){
        return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"invalid input details","data"=>json_decode("{}")]);
      }
      //echo $pwd = Hash::make($request->password).'      ='.$request->email; die;
      $validationChk = User::where('phone',$request->phone)->get();
      
      
      if($validationChk->count()==0){
        return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"invalid credentials","data"=>json_decode("{}")]);          
      }else if($validationChk[0]->status != '1'){
        return response()->json(["status"=>$status,"responseCode"=>"NP997","message"=>"User not verified","data"=>json_decode("{}")]);          
      }
      

      $credentials = $request->only('phone', 'otp');                

      
      try {
        $myTTL = 43200; //minutes
        JWTAuth::factory()->setTTL($myTTL);            
          if (! $token = JWTAuth::attempt($credentials, ['status'=>'1'])) {            
              $message = 'invalid_credentials';                
              return response()->json(['status'=>$status,"responseCode"=>"NP997",'message'=>$message,'data'=>json_decode("{}")]);
          }
      } catch (JWTException $e) {
          $message = 'could_not_create_token';
          return response()->json(['status'=>$status,"responseCode"=>"NP997",'message'=>$message,'data'=>json_decode("{}")]);            
      }        
      $user  = JWTAuth::user();


      unset($user->otp);
      unset($user->verified_otp);
      $user->token = $token;
      $user->remember_token = $token;
      $user->device_token = (isset($request->device_token)) ? $request->device_token : '';
      $user->device_id = (isset($request->device_id)) ? $request->device_id : '';
      //$this->SendSms($request->phone,'Welcome to LivFit Your OTP is: '.$otp);
      $user->save();
      unset($user->remember_token);
      $status = 1;     
      $user->language_id = "null";
      if(isset($user->userProfile)){
        foreach($user->userProfile as $k=>$v){
          if($v->default=='Y'){
            $user->roll_no =  $v->roll_no;
            $user->language_id = $v->language_id;
            $user->district_id = $v->district_id;
            $user->class_id = $v->class_id;
            $user->school_id = $v->school_id;
          }
        }
      }

      return response()->json(['status'=>1,"responseCode"=>"APP001",'message'=>"Successful Login",'data'=>$user]);
  }

  public function apilogout(Request $request){
    
    try{        
      JWTAuth::invalidate(JWTAuth::parseToken()); 
      //JWTAuth::setToken($token)->invalidate();
      return response()->json(['status'=>1,"responseCode"=>"APP001",'message'=>'','data'=>json_decode("{}")]);
    }catch(Exception $e){
      return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }
  
 
  public function getAuthenticatedUser() { 
       $status = 0;   
      try {

              if (! $user = JWTAuth::parseToken()->authenticate()) {
                //return response()->json(['user_not_found'], 404);
                return response()->json(['status'=>$status,'message'=>'user_not_found','data'=>json_decode("{}")]);
              }

      } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

          //return response()->json(['token_expired'], $e->getStatusCode());
          return response()->json(['status'=>$status,'message'=>'token_expired','data'=>json_decode("{}")]);

      } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

        //return response()->json(['token_invalid'], $e->getStatusCode());
        return response()->json(['status'=>$status,'message'=>'token_invalid','data'=>json_decode("{}")]);

      } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['status'=>$status,'message'=>'token_absent','data'=>json_decode("{}")]);
        //return response()->json(['token_absent'], $e->getStatusCode());
      }
      $status = 1;
      return response()->json(compact('user'));
 }
     
   

   public function changePassword(Request $request){
    try{
        
      $status = 0;
            $message = "";
          
            
            $user  = JWTAuth::user();  

            $validator = Validator::make($request->all(), [
                'old_password' => 'required',                                
                'new_password' => 'min:6|required_with:password_confirmation|same:password_confirmation',                                
                'password_confirmation' => 'required|min:6',                                
            ]);           
            if($validator->fails()){
              $error = json_decode(json_encode($validator->errors()));
              if(isset($error->old_password)){
                $message = $error->old_password[0];
              }else if(isset($error->new_password)){
                $message = $error->new_password[0];
              }else if(isset($error->password_confirmation)){
                $message = $error->password_confirmation[0];
              }
              return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
            } 

            if(!Hash::check($request->old_password, $user->password)){
              return response()->json(['status'=>$status,'message'=>'old password incorrect','data'=>json_decode("{}")]);
            }else{            
              User::where('email', $user->email)->update(['password'=>Hash::make($request->new_password)]);
              
              return response()->json(['status'=>1,'message'=>$message, 'data'=>json_decode("{}")]);
            }            

            return response()->json(['status'=>1,
            'message'=>$message,                                
            'data'=>[]
            ]);
            
                                          
        }catch(Exception $e){
      
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        }   
    }

      /**
     * Edit event method
     * @return success or error
     * 
     * */
    public function editMyProfile(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        $user  = JWTAuth::user();
        

        $user->id = $user->id; 
        $user->name = (isset($request->name)) ? $request->name : $user->name;  

        if(isset($request->email)){
          $email_count = \App\User::where('email',$request->email)->where('id','<>',$user->id)->count();
          if(!$email_count){
            $user->email = $request->email;
          }else{
            return response()->json(['status'=>$status,'message'=>'Email already exist','data'=>json_decode("{}")]);                    
          }
        }

        if(isset($request->phone)){
          $phone_count = \App\User::where('phone',$request->phone)->where('id','<>',$user->id)->count();
          if(!$phone_count){
            $user->phone = $request->phone;
          }else{
            return response()->json(['status'=>$status,'message'=>'Phone number already exist','data'=>json_decode("{}")]);                    
          }
        }
        
        $user->phone = (isset($request->phone)) ? $request->phone : $user->phone;
        $user->gender = (isset($request->gender)) ? $request->gender : $user->gender;

        if(isset($request->class_id) && isset($request->school_id) && isset($request->district_id)){
          $profile = \App\UserProfile::where('user_id',$user->id)->where('default','Y')->get();
          
          $update_flag = 0;
          if($profile->count()){
            foreach($profile as $k=>$v){
              if(($request->class_id == $v->class_id) && ($request->school_id == $v->school_id) && ($request->district_id == $v->district_id)){
                $update_flag++;
              }
            }
          }
	//dd($update_flag,$request->all());
          if($update_flag){
            \App\UserProfile::where('user_id', $user->id)->update(['default' => 'Y']);
            $profileObj = new \App\UserProfile();
            //$profileObj->language_id = $request->language_id;
            $profileObj->class_id = $request->class_id;
            $profileObj->school_id = $request->school_id;          
            $profileObj->district_id = $request->district_id;
            $profileObj->user_id = $user->id;
	    $profileObj->roll_no = $request->roll_number;
            $profileObj->default = 'Y';
            $profileObj->save();
          }
        }
	

        if(isset($_FILES['file']['name'])) {                
          $upload_handler = new UploadFile();
          $path = public_path('uploads/users'); 
          $data = $upload_handler->uploadByName($path,'file','users');
          $res = json_decode($data);           
          if($res->status=='ok'){
            $user->image = $res->path;
            $user->file_path = $res->img_path;                                
          }                                                 
        } 
	
        if(!$user->save()){ 
            return response()->json(['status'=>$status,'message'=>'Unable to save','data'=>$user]);                    
        }else{       
		$user['roll_no'] = $request->roll_number;
            return response()->json(['status'=>1,'message'=>'Profile updated successfully','data'=>$user]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }

    public function getMyProfile(Request $request){
    $tr = new GoogleTranslate();
    $tr->setSource(); // Translate from English
    $lang = $request->language;
    $tr->setTarget($lang);
      try{
        $status = 1;
        $message = "";
              
        $user  = JWTAuth::user();        
        if(!isset($user)){
          return response()->json(['status'=>0,'message'=>'User does not exist','data'=>json_decode("{}")]);                                      
        }
        
        $data = \App\User::getUserById($user->id);
	//dd($data,$data->userProfile);
        if(!$data->userProfile->isEmpty()){
          foreach($user->userProfile as $k=>$v){
            if($v->default=='Y'){
              
              //dd($v->district->district_name);
              //$data->language_id = $v->language_id;
              //$data->language_name = $v->language->language_name;
              $data->district_id = $v->district_id;
              $data->district_name = $v->district->district_name;
              $data->class_id = $v->class_id;
              $data->class_name = $v->class->class_name;
              $data->school_id = $v->school_id;         
              $data->school_name = $v->school->school_name; 
	      $data->roll_no = $v->roll_no;  
            }else{
		
	      //$data['language_id'] = "";
              //$data['language_name'] = "";
              $data['district_id'] = "";
              $data['district_name'] = "";
              $data['class_id'] = "";
              $data['class_name'] = "";
              $data['school_id'] = "";         
              $data['school_name'] = "";
	      $data['roll_no'] = "";
	}
          }
        }else{
		
	      //$data['language_id'] = "";
              //$data['language_name'] = "";
              $data['district_id'] = "";
              $data['district_name'] = "";
              $data['class_id'] = "";
              $data['class_name'] = "";
              $data['school_id'] = "";         
              $data['school_name'] = "";
	      $data['roll_no'] = "";
	}
	//$data->name = $tr->translate($data->name);
	//$data->gender = $tr->translate($data->gender);
	//$data->social_type = $tr->translate($data->social_type);
        return response()->json(['status'=>$status,'message'=>'','data'=>$data]);                                      
      }catch(Exception $e){
        return response()->json(['status'=>0,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }

    public function register(Request $request)
    {
      $status = 0;
      $message = "";
      DB::beginTransaction();
      try{
          

           $validator = Validator::make($request->all(), [
               'name' => 'required|string|max:255',
               'email' => 'required|string|max:255|unique:users',                                          
               'gender' => 'required|string|max:15',  
               'class_id'=> 'required',               
               'school_id' => 'required',
               'district_id' => 'required',
               //'language_id' => 'required',
               'phone' =>  'required|string|max:10|unique:users',
               'device_id'=>'required'
           ]);

           $data = [];          
          //  $data['email'] = $request->get('email');
          //  $data['name'] = $request->get('name');
          //  $data['supportEmail'] = config('mail.supportEmail');
          //  $data['website'] = config('app.site_url');  
          //  $data['site_name'] = config('app.site_name');                     
                  
          // $data['subject'] = 'Registration OTP from '.config('app.site_name'); 
           $otp = rand(111111,999999);  
           $data['otp'] = $otp;   
                                  
           //$validator->errors()
           if($validator->fails()){
             $error = json_decode(json_encode($validator->errors()));
             if(isset($error->name[0])){
               $message = $error->name[0];
             }else if(isset($error->email)){
               $message = $error->email[0];
             }else if(isset($error->phone)){
               $message = $error->phone[0];
             }else if(isset($error->gender)){
              $message = $error->gender[0];
             }else if(isset($error->device_id)){
              $message = $error->device_id[0];
             }else if(isset($error->class_id)){
              $message = $error->class_id[0];
             }else if(isset($error->district_id)){
              $message = $error->district_id[0];
             }else if(isset($error->school_id)){
              $message = $error->school_id[0];
             }
             return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
           }
           
           $social_id = isset($request->social_id) ? $request->social_id : 0;
           $social_type = isset($request->type) ? $request->type : '';
           $user = User::create([
             'name' => $request->get('name'),
             'email' => $request->get('email'),
             'phone' => $request->get('phone'),                         
             'social_id' => $social_id,
             'social_type' => $social_type,
             'status' => '0',             
             'password' => Hash::make($request->get('123456@!')),
             'verified_otp' => 0,                          
             'gender' => $request->get('gender'),             
             'device_id' => $request->get('device_id'),
	     'device_token' => $request->get('device_token'),
             'otp' => $otp
         ]);
	
         $user->assignRole('Student');
	
         $token = JWTAuth::fromUser($user);          
         //JWTAuth::setToken($token);
	 DB::table('users')->where('id',$user->id)->update(['remember_token' => $token]);
         $userProfile = new \App\UserProfile();         
         $userProfile->user_id = $user->id;
         $userProfile->class_id = $request->class_id;
         $userProfile->school_id = $request->school_id;
         $userProfile->district_id = $request->district_id;
	 $userProfile->roll_no = $request->roll_number;
         //$userProfile->language_id = $request->language_id;
         $userProfile->default = 'Y';
	
         if($userProfile->save()){           
           DB::commit();
	   $diviceIds = [$request->device_token];
	   if(isset($diviceIds)){
                    $suscription_title = "User Registration";
                    $suscription_msg = "User Registered Successfully";
                    $this->sendNotification($diviceIds,'',$suscription_title,$suscription_msg);
                    Mail::to($request->get('email'))->send(new RegistrationMail(['user'=>$user,'password'=>$request->get('123456@!')]));
            }
           $message = "User Registered Successfully";
           return response()->json(["status"=>1,"message"=>$message,"data"=>compact('user','token')]);        
         }else{
           return response()->json(["status"=>0,"message"=>'Unable to send email',"data"=>json_decode("{}")]);        
         }                              
       } catch(Exception $e){
         DB::rollBack();
         return response()->json(['status'=>$status,'message'=>'asdfasdf','data'=>json_decode("{}")]);
       }              
    }

    /**
     * Edit event method
     * @return success or error
     * 
     * */
    public function getProfile(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        $user  = JWTAuth::user();

        if(isset($user->token)){
          unset($user->token);
          unset($user->verified_otp);
          unset($user->status);
        }
        
        if(!$user->id){          
            return response()->json(['status'=>$status,'message'=>'Profile does not exist','data'=>$user]);                    
        }else{          
            return response()->json(['status'=>1,'message'=>'','data'=>$user]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'User update Error','data'=>json_decode("{}")]);                    
      }
              
    }

    

    public function testNotification(){
      $diviceIds = ['eQtR_6F5Sqej3G9fvHO8hX:APA91bEjlBxhRIOYjZCvtMR0AxqUxpmkTT7urVouT927nMF84sJLArmQKAFcrIKr2jcjXp__CPWUPrp0oMJ-ShOjlVt0MsNykO4AHEUfTWWum_OyIwDvLJLX3spngjgsFl5H15Bb7v61'];
      $data = $this->sendNotification($diviceIds,'','Subscription success','Your subscription is successfully added');
      return response()->json(["status"=>1,"message"=>'message send',"data"=>json_decode($data)]);
    }

    

     
    public function addReview(Request $request){
    
      try{                
        $status = 0;
        $message = "";
        $user  = JWTAuth::user();

        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'invalid token','data'=>json_decode("{}")]);  
        }

        $validator = Validator::make($request->all(), [
          'rating' => 'required|min:1|max:5',
          'trainer_id' => 'required',
          'type'=>'required',
          'schedule_id'=>'required'
        ]);

        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->rating[0])){
            $message = $error->rating[0];
          }else if(isset($error->trainer_id[0])){
            $message = $error->trainer_id[0];
          }else if(isset($error->schedule_id[0])){
            $message = $error->schedule_id[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }
        $message1 = '';
        if(isset($request->message)){
          $message1 = $request->message;
        }
        $review = Review::where('user_id',$user->id)
        ->where('rating_to',$request->trainer_id)
        ->where('schedule_id',$request->schedule_id)
        ->where(DB::raw('DATE(created_at)'),date('Y-m-d'))->first();
        if(isset($review->id)){
          return response()->json(['status'=>0,'message'=>'one rating is allowed for one day','data'=>json_decode("{}")]);  
        }
        //echo $request->schedule_id; die;
        $inputs = ['user_id' => $user->id,
        'type'=>$request->type,
        'rating_to'=>$request->trainer_id,
        'schedule_id'=>$request->schedule_id,
        'comment'=>$message1,'rating' => $request->rating];

        $data = Review::create($inputs);

        return response()->json(['status'=>1,'message'=>'Message sent','data'=>$data]);
      }catch(Exception $e){
        return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'Not able to logout','data'=>json_decode("{}")]);
      }
      
    }

    public function getMyNotifications(Request $request){
    
      try{                
        $status = 0;
        $message = "";
        $user  = JWTAuth::user();
        $result = \App\Notification::getMyNotifications($user->id);
        return response()->json(['status'=>1,"responseCode"=>"APP001",'message'=>'','data'=>$result]);
      }catch(Exception $e){
        return response()->json(['status'=>0,"responseCode"=>"NP997",'message'=>'Not able to logout','data'=>json_decode("{}")]);
      }
      
    }
    function UserSubscriptions()
    {
      try{
        $user = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
        }

        $result = \App\Subscription::join('users','users.id','subscriptions.user_id')->join('plans','plans.id','subscriptions.plan_id')->select('users.name as user_name','subscriptions.plan_id as plan_id','plans.plan_name as plan_name','subscriptions.status as status','subscriptions.created_at','subscriptions.updated_at','subscriptions.start_date','plans.plan_rate as plan_rate','plans.days as days')->where(['user_id'=>$user->id])->get();
	//dd($result[0]['days'],$result[0]['start_date']);
	for($i=0;$i<count($result); $i++){
	$startDate = $result[$i]['start_date'];
	$newData = $result[$i]['days'];
	$result[$i]['renewal_date'] = date('Y-m-d', strtotime($startDate. ' + '.$newData.' days'));
	$result[$i]['Payment_method'] = "Razor Pay";
	}
        if(count($result) > 0){
          return response()->json(['status'=>1,'message'=>'Subscription Data','data'=>$result]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'Data not found.','data'=>json_decode("{}")]);                    
        }
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }

}