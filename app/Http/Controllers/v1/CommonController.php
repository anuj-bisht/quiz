<?php
//amary@321! amary
namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Common;
use App\Http\Controllers\Traits\SendMail;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Config;
use JWTAuth;
use DB;

class CommonController extends Controller
{
    use Common,SendMail;
    /**
   * ge Home pate method
   * @return success or error
   * 
   * */
  public function getAllDistrict(Request $request){
    //$tr = new GoogleTranslate();
    //$tr->setSource(); // Translate from English
    //$lang = $request->language;
    //$tr->setTarget($lang);
    try{
      $status = 0;
      $message = "";    
      
      $params = $this->commonQueryArr();
      $params['status'] = 'Y';
      $params['get_result'] = true;
      
      $result = \App\District::getAllDistrict($params);
      if($result->count() > 0){
	  //for($i = 0; $i<$result->count(); $i++){
	//	$result[$i]->district_name = $tr->translate($result[$i]->district_name);
         // }
          return response()->json(['status'=>1,'message'=>'','data'=>$result]);                    
      }else{
          return response()->json(['status'=>$status,'message'=>'No district Found','data'=>json_decode("{}")]);                    
      }   
    }catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Exception Error','data'=>json_decode("{}")]);                    
    }
            
  }

  

  public function getAllDistrictById(Request $request){
    
    try{
      $status = 0;
      $message = "";    
      
      $validator = Validator::make($request->all(), [
          'site_id' => 'required',                                                    
      ]);           
      if($validator->fails()){
        $error = json_decode(json_encode($validator->errors()));
        if(isset($error->site_id)){
          $message = $error->site_id[0];
        }
        return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
      } 

      $site_id = $request->site_id;

      $result = SiteData::getDivisionBySite($site_id);
      if($result->count() > 0){
          return response()->json(['status'=>1,'message'=>'','data'=>$result]);                    
      }else{
          return response()->json(['status'=>$status,'message'=>'No data Found','data'=>json_decode("{}")]);                    
      }   
    }catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Exception Error','data'=>json_decode("{}")]);                    
    }
  }
  public function getAllBlocksByDistrictId(Request $request){
    
    try{
      $status = 0;
      $message = "";    
      
      $validator = Validator::make($request->all(), [
          'district_id' => 'required',                                                    
      ]);           
      if($validator->fails()){
        $error = json_decode(json_encode($validator->errors()));
        if(isset($error->district_id)){
          $message = $error->district_id[0];
        }
        return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
      } 

      $district_id = $request->district_id;

      $result = \App\Block::where('district_id',$district_id)->get();
      if($result->count() > 0){
          return response()->json(['status'=>1,'message'=>'','data'=>$result]);                    
      }else{
          return response()->json(['status'=>$status,'message'=>'No data Found','data'=>json_decode("{}")]);                    
      }   
    }catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Exception Error','data'=>json_decode("{}")]);                    
    }
  }

    public function getAllClasses(Request $request){
    //$tr = new GoogleTranslate();
    //$tr->setSource(); // Translate from English
    //$lang = $request->language;
    //$tr->setTarget($lang);
      try{
        $status = 0;
        $message = "";    
        
        $params = $this->commonQueryArr();
        $params['status'] = 'Y';
        $params['get_result'] = true;
        
        $result = \App\Classes::getAllClasses($params);
        if($result->count() > 0){
	  //for($i = 0; $i<$result->count(); $i++){
	//	$result[$i]->class_name = $tr->translate($result[$i]->class_name);
         // }
            return response()->json(['status'=>1,'message'=>'Class List','data'=>$result]);                    
        }else{
            return response()->json(['status'=>$status,'message'=>'No classes Found','data'=>json_decode("{}")]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Exception Error','data'=>json_decode("{}")]);                    
      }
              
    }

    public function getAllSchool(Request $request){
    //$tr = new GoogleTranslate();
    //$tr->setSource(); // Translate from English
    //$lang = $request->language;
    //$tr->setTarget($lang);
      try{
        $status = 0;
        $message = "";    
        
        $params = $this->commonQueryArr();
        
        $params['status'] = 'Y';
        $params['get_result'] = true;
	$params['district_id'] = $request->district_id;
        
        //$result = \App\School::getAllSchool($params);
	$result = \App\School::select('schools.*','districts.district_name')
                        ->join('districts','districts.id','=','schools.district_id')
			->where('schools.district_id','=',$params['district_id']);
	if($params['get_result']){
            $result = $result->get();
        }
	
        if($result->count() > 0){
	  //for($i = 0; $i<$result->count(); $i++){
	//	$result[$i]->school_name = $tr->translate($result[$i]->school_name);
	//	$result[$i]->school_address = $tr->translate($result[$i]->school_address);
	//	$result[$i]->district_name = $tr->translate($result[$i]->district_name);
        //  }
            return response()->json(['status'=>1,'message'=>'School Data','data'=>$result]);                    
        }else{
            return response()->json(['status'=>$status,'message'=>'No school Found','data'=>json_decode("{}")]);                    
        }   
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Exception Error','data'=>json_decode("{}")]);                    
      }
              
    }
            
  

  public function contactus(Request $request){
    try{
      $status = 0;
      $message = "";
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'message' => 'required'
      ]);

      if($validator->fails()){
        $error = json_decode(json_encode($validator->errors()));
        if(isset($error->name[0])){
          $message = $error->name[0];
        }else if(isset($error->email[0])){
          $message = $error->email[0];
        }else if(isset($error->phone[0])){
          $message = $error->phone[0];
        }else if(isset($error->message[0])){
          $message = $error->message[0];
        }
        return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
      }
      
      $obj = new \App\Contactus();
      $obj->name = $request->name;
      $obj->phone = $request->phone;
      $obj->email = $request->email;
      $obj->message = $request->message;                        

      if($obj->save()){      
          $data = [];
          $data['to_email'] = config('app.MAIL_FROM_ADDRESS');
          $data['from'] = $request->email;         ;
          $data['subject'] = 'Contact enquiry';
          $data['name'] = $request->name;
          $data['email'] = $request->email;
          $data['phone'] = $request->phone;
          $data['message1'] = $request->message;
          $this->SendMail($data,'contact');

          return response()->json(['status'=>1,'message'=>'message sent','data'=>json_decode("{}")]);                    
      }else{          
          return response()->json(['status'=>$status,'message'=>'error','data'=>json_decode("{}")]);                    
      }   
    }catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
    }
  }

  public function getsetting(Request $request){
    $tr = new GoogleTranslate();
    //$tr->setSource(); // Translate from English
    $lang = $request->language;
    $tr->setTarget($lang);
    try{        
      	$data = \App\Setting::where('id',1)->first();
      	//$data->terms_and_condition = $tr->translate($data->terms_and_condition);
	//$data->privacy_policy = $tr->translate($data->privacy_policy);
	//$data->faq = $tr->translate($data->faq);
      	return response()->json(['status'=>1,'message'=>'settings','data'=>$data]);
    }catch(Exception $e){
      	return response()->json(['status'=>0,'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }

  public function getSubject(Request $request){
    $tr = new GoogleTranslate();
    $tr->setSource(); // Translate from English
    $lang = $request->language;
    $tr->setTarget($lang);
    $params = $this->commonQueryArr();
    $params['status'] = 'Y';
    $params['get_result'] = true;
    $result = new \stdClass();

    try{        
      $data = \App\Subject::getAllSubject($params);
      if($data->count()){
        $result = \App\Subject::filterResult($data);
	for($i = 0; $i<$data->count(); $i++){
		//$result[$i]->subject_name = translate($result[$i]->subject_name);
		//$result[$i]->class_name = GoogleTranslate::trans($result[$i]->class_name, $lang);
		//$result[$i]->classes->class_name = GoogleTranslate::trans($result[$i]->classes->class_name, $lang);
          }
      }
      return response()->json(['status'=>1,'message'=>'','data'=>$result]);
    }catch(Exception $e){
      return response()->json(['status'=>0,'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }

  public function getChapterBySubject(Request $request){
    $tr = new GoogleTranslate();
    //$tr->setSource(); // Translate from English
    $lang = $request->language;
    $tr->setTarget($lang);
    try{        
        $params = $this->commonQueryArr();
        $params['status'] = 'Y';
        $params['get_result'] = true;
        $result = new \stdClass();
      
        $status = 0;
        $message = "";
        
        $validator = Validator::make($request->all(), [
          'subject_id' => 'required'
        ]);

        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->name[0])){
            $message = $error->name[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }

        $params['subject_id'] = $request->subject_id;
	$params['language'] = $request->language;
        
        $data = \App\Chapter::getChapterBySubject($params);
	
        if($data->count()){
          $result = \App\Chapter::filterResult($data);
	  for($i = 0; $i<$data->count(); $i++){
        	//$result[$i]->class_name = $tr->translate($result[$i]->class_name);
        	//$result[$i]->chapter_name = $tr->translate($result[$i]->chapter_name);
		//$result[$i]->subject_name = $tr->translate($result[$i]->subject_name);
		//dd($result[$i]->chapter->chapter_name);
		//$result[$i]->classes->class_name = $tr->translate($result[$i]->classes->class_name);
		//$result[$i]->subject->subject_name = $tr->translate($result[$i]->subject->subject_name);
          }
        }
        return response()->json(['status'=>1,'message'=>'','data'=>$result]);
    }catch(Exception $e){
      return response()->json(['status'=>0,'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }


  public function getPlanList(Request $request){
    $tr = new GoogleTranslate();
    $headers = apache_request_headers();
    //$tr->setSource(); // Translate from English
    $lang = $request->language;
    //$tr->setTarget($lang);
	
	//dd($headers['X-localization']);
    $params = $this->commonQueryArr();
    $params['status'] = 'Y';
    $params['get_result'] = true;
    $result = new \stdClass();

    try{        
      
      $data = \App\Plan::getAllList($params);
  
      if($data->count()){
      for($i = 0; $i<$data->count(); $i++){
        //$data[$i]->plan_name = $tr->translate($data[$i]->plan_name);//dd($data[$i]->plan_name);
        //$data[$i]->description = $tr->translate($data[$i]->description);
        //$data[$i]->plan_rate = $tr->translate($data[$i]->plan_rate);
      }
  
        return response()->json(['status'=>1,'message'=>'Plan List','data'=>$data]);
      }else{
        return response()->json(['status'=>0,'message'=>'No plan found','data'=>json_decode("{}")]);
      }      
    }catch(Exception $e){
      return response()->json(['status'=>0,'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }

  public function getPlanDetails(Request $request){
    $tr = new GoogleTranslate();
    $headers = apache_request_headers();
    //$tr->setSource(); // Translate from English
    $lang = $request->language;
    //$tr->setTarget($lang);
    //dd($headers['X-localization']);
    $params = $this->commonQueryArr();
    $params['status'] = 'Y';
    $params['get_result'] = true;
    $result = new \stdClass();

    try{        
      
      $data = \App\Plan::where('id',$request->plan_id)->get();
  
      if($data->count()){
      for($i = 0; $i<$data->count(); $i++){
        //$data[$i]->plan_name = $tr->translate($data[$i]->plan_name);//dd($data[$i]->plan_name);
        //$data[$i]->description = $tr->translate($data[$i]->description);
        //$data[$i]->plan_rate = $tr->translate($data[$i]->plan_rate);
      }
  
        return response()->json(['status'=>1,'message'=>'Plan Details','data'=>$data]);
      }else{
        return response()->json(['status'=>0,'message'=>'No plan found','data'=>json_decode("{}")]);
      }      
    }catch(Exception $e){
      return response()->json(['status'=>0,'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }

  public function getExamList(Request $request){
    // $tr = new GoogleTranslate();
    //$tr->setSource(); // Translate from English
    $lang = $request->language;
    // $tr->setTarget($lang);
    $params = $this->commonQueryArr();
    $params['status'] = 'Y';
    $params['get_result'] = true;
    if(isset($request->exam_type)){
      $params['exam_type'] = $request->exam_type;
    }
    $result = new \stdClass();

    $user  = JWTAuth::user(); 
    if(!isset($user->id)){
      return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
    }

    try{        
      
      $data = \App\Exam::getAllExam($params);

      if($data->count()){
        $result = \App\Exam::filterResult($data);
	//dd($result[0]->description);
	for($i = 0; $i<$data->count(); $i++){
		//$result[$i]->title = $tr->translate($result[$i]->title);
        	//$result[$i]->description = $tr->translate($result[$i]->description);
        	//$result[$i]->chapter_name = $tr->translate($result[$i]->chapter_name);
		//$result[$i]->subject_name = $tr->translate($result[$i]->subject_name);
		//$result[$i]->chapter->chapter_name = $tr->translate($result[$i]->chapter->chapter_name);
		//$result[$i]->chapter->subject->subject_name = $tr->translate($result[$i]->chapter->subject->subject_name);
		//$result[$i]->subject->subject_name = $tr->translate($result[$i]->subject->subject_name);
        }
	$subscription = DB::table('subscriptions')->where(['user_id'=>$user->id,'status' => 'active'])->count();
	  if($subscription > 0){
		$sbuscription = "Yes";
	  }else{
		$sbuscription = "No";
	  }
        //echo '<pre>';print_r($result->count()); die;
        return response()->json(['status'=>1,'message'=>'Exam Data','is_subscription' => $sbuscription,'data'=>$result]);
      }else{
        return response()->json(['status'=>0,'message'=>'No data found','data'=>json_decode("{}")]);
      }      
    }catch(Exception $e){
      return response()->json(['status'=>0,'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }

  public function myExamList(Request $request){
    $tr = new GoogleTranslate();
    //$tr->setSource(); // Translate from English
    $lang = $request->language;
    $tr->setTarget($lang);
    $params = $this->commonQueryArr();
    $params['status'] = 'Y';
    $params['get_result'] = true;
    $result = new \stdClass();

    try{        
      
      $user  = JWTAuth::user(); 

      $user_data = \App\UserProfile::getUserProfile($user->id);
      
      if($user_data->count()){               
        //$data = \App\UserProfile::filterResult($user_data);

        $data = \App\Chapter::getChapterByProfile($user_data);
        if($data->count()){
          return response()->json(['status'=>1,'message'=>'','data'=>$data]);
        }

        return response()->json(['status'=>1,'message'=>'','data'=>json_decode("{}")]);

      }else{
        return response()->json(['status'=>0,'message'=>'No plan found','data'=>json_decode("{}")]);
      }      
    }catch(Exception $e){
      return response()->json(['status'=>0,'message'=>'Not able to logout','data'=>json_decode("{}")]);
    }
    
  }

  public function search(Request $request){
    
    $params = $this->commonQueryArr();
    $params['status'] = 'Y';
    $params['get_result'] = true;
    $result = new \stdClass();

    try{        

      $status = 0;
      $message = "";
      $validator = Validator::make($request->all(), [
        'search_keyword' => 'required',        
      ]);


      if($validator->fails()){
        $error = json_decode(json_encode($validator->errors()));
        if(isset($error->search_type[0])){
          $message = $error->search_type[0];
        }
        return response()->json(["status"=>$status,"message"=>"Invalid input","data"=>json_decode("{}")]);
      }

      $user  = JWTAuth::user(); 
           
      $search_keyword = $request->search_keyword;
      //dd($search_keyword);
      $user_profile = \App\UserProfile::getUserProfile($user->id);
      // dd($user_profile);
      
      $data = \App\Chapter::select('chapters.*','subjects.subject_name','classes.class_name','exams.id as exam_id')
      ->join('subjects','chapters.subject_id','=','subjects.id')
      ->join('classes','classes.id','=','subjects.class_id')
      ->join('exams','exams.subject_id','=','subjects.id')
      ->where('classes.id',$user_profile->class_id)
      ->where('subjects.subject_name','LIKE',"%".$search_keyword."%")
      //->OrWhere('classes.class_name','LIKE',"%".$search_keyword."%")
      ->OrWhere('chapters.chapter_name_hindi','LIKE',"%".$search_keyword."%");
      //->OrWhere('exams.title','LIKE',"%".$search_keyword."%");
	//->groupBy('exams.class_id');
      $data = $data->get();
	//dd($data[0]);
      if($data->count()){                       
        return response()->json(['status'=>1,'message'=>'','data'=>$data]);
      }else{
        return response()->json(['status'=>0,'message'=>'No data found','data'=>json_decode("{}")]);
      }      
    }catch(Exception $e){
      return response()->json(['status'=>0,'message'=>$e->getMessage(),'data'=>json_decode("{}")]);
    }
    
  }

   
}
