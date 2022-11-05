<?php

namespace App\Http\Controllers\v1;


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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\StartExam;
use App\Exam;
use App\Option;
use App\User;
use App\Mail\FinishExamMail;
use Stichoza\GoogleTranslate\GoogleTranslate;




class ExamController extends Controller
{
  use Common,SendMail;
      
    
    public function startExam(Request $request){
      

      try{

        // $tr = new GoogleTranslate();
        // $lang = $request->language;
        // $tr->setTarget($lang);

        $status = 0;
        $message = "";

        $user  = JWTAuth::user(); 
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
        }
              
        $validator = Validator::make($request->all(), [
          'exam_id' => 'required'                           
        ]);

        $params = [];  
        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->exam_id[0])){
            $message = $error->exam_id[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }
        $exam_id = $request->exam_id; 

        $exam_auth = \App\Exam::checkUserExamClass($user->id,$exam_id);
        // dd($user->id,$exam_id);
        
        $user_profile = \App\UserProfile::getUserProfile($user->id);
        $exam_data = \App\Exam::getExamById($exam_id);
        // dd($exam_data);
        
        if(!$exam_auth){
          return response()->json(['status'=>0,'message'=>'You are not authrized to take this exam','data'=>json_decode("{}")]);                    
        }

        if(!isset($user_profile->id)){
          return response()->json(['status'=>0,'message'=>'Please update your profile to get exam','data'=>json_decode("{}")]);                    
        }

        if(!isset($exam_data->id)){
          return response()->json(['status'=>0,'message'=>'No Exam exist with this id','data'=>json_decode("{}")]);                    
        }
            
        
        $chapter_id = ($exam_data->exam_type=='Chapter') ? $exam_data->chapter_id:false;
        // dd($chapter_id);
        $params = [
          'subject_id'=>$exam_data->subject_id,
          'class_id'=>$exam_data->class_id,
          'no_of_question'=>$exam_data->no_of_question,
          'chapter_id'=>explode(',',$exam_data->chapter_id),
	  'language'=>$request->language
        ];
        
        $questionList = \App\Question::getRandomQuestion($params);
        // for($i=0;$i<count($questionList); $i++)
        // {
        //   $questionList[$i]->question = $tr->translate($questionList[$i]->question);
        // }
        $obj = new \App\StartExam();
        $obj->user_id = $user->id;
        $obj->user_profile_id = $user_profile->id;
        $obj->exam_id = $request->exam_id;            
        $obj->exam_date = date('Y-m-d H:i:s');
        $obj->start_timer = $exam_data->duration;
        $obj->status = 'Started';
        $obj->result = 'Incomplete';
        if($obj->save()){
	      $obj['duration'] = $exam_data->duration;
          Cache::forever("exam_$obj->id", $questionList);
          //Cache::forget('key');
      
          return response()->json(['status'=>1,'message'=>'Exam Data','data'=>[
            'exam'=>$obj,'questions'=>$questionList
          ]]);                      
          
        }else{
          return response()->json(['status'=>$status,'message'=>'Can not start exam','data'=>json_decode("{}")]);                      
        }       

      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }


    public function finishExam1(Request $request){
      try{
        

        $status = 0;
        $message = "";

        $user  = JWTAuth::user(); 
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
        }
              
        $validator = Validator::make($request->all(), [
          'exam_id' => 'required'                           
        ]);
        

        $params = [];  
        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->exam_id[0])){
            $message = $error->exam_id[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }
        $exam_id = $request->exam_id; 

        $cache_id = "exam_".$exam_id;
                
        if(Cache::has($cache_id)) {
          $questionList = Cache::get($cache_id);
          //dd($value);
        }else{
          return response()->json(['status'=>0,'message'=>'No Exam exist with this id','data'=>json_decode("{}")]);                    
        }

        $total_question = $questionList->count();
        
        if(isset($request->data) && count($request->data)>0){
          if(count($request->data) != $questionList->count()){
            return response()->json(['status'=>0,'message'=>'Question list mismatch','data'=>json_decode("{}")]);                    
          }
          $marks = 0;
          $finalQuestion = [];
          foreach($request->data as $k=>$v){
            foreach($questionList as $k1=>$v1){
              if($v1->id==$v->id){
                $optionData = \App\Option::where('id',$v->id)->first();
                if(isset($optionData->id) && $optionData->is_correct=='Y'){                  
                  $request->data[$k]['is_correct'] = 'Y';
                  $marks++;
                } else {
                  $request->data[$k]['is_correct'] = 'N';
                }

                $finalQuestion[$k] = $v;
              }
            }
          }

          $startExam = \App\StartExam::findOrFail($request->exam_id);

          $percentage = ($marks*100)/$total_question;

          $startExam->status = 'Completed';
          $startExam->result = 'Pass';
          $startExam->end_timer = time();
          $startExam->percentage = $percentage;

          $resultData = ['questions'=>$request->data,'result'=>$startExam];

          if($startExam->save()){
            return response()->json(['status'=>0,'message'=>'','data'=>$resultData]);                    
          }else{
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);                    
          }

          
        }


        // $exam_auth = \App\Exam::checkUserExamClass($user->id,$exam_id);
        // $user_profile = \App\UserProfile::getUserProfile($user->id);
        // $exam_data = \App\Exam::getExamById($exam_id);
        
        // if(!$exam_auth){
        //   return response()->json(['status'=>0,'message'=>'You are not authrized to take this exam','data'=>json_decode("{}")]);                    
        // }

        // if(!isset($user_profile->id)){
        //   return response()->json(['status'=>0,'message'=>'Please update your profile to get exam','data'=>json_decode("{}")]);                    
        // }

        // if(!isset($exam_data->id)){
        //   return response()->json(['status'=>0,'message'=>'No Exam exist with this id','data'=>json_decode("{}")]);                    
        // }

        
                     

      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }

    public function finishExam(Request $request){
      
      try{
        // return response()->json(['status'=>1,'message'=>'check status run ','data'=>JWTAuth::user()]);
        $status = 0;
        $message = "";
	
        $user  = JWTAuth::user(); 
        
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
        }
        
        $validator = Validator::make($request->all(), [
          'exam_id' => 'required'                           
        ]);
        

        $params = [];  
        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->exam_id[0])){
            $message = $error->exam_id[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }
        $exam_id = $request->exam_id; 
	
        $cache_id = "exam_".$exam_id;
        
        $startExam = StartExam::findOrFail($request->exam_id);
        $subjectid = Exam::select('class_id','subject_id')->where('id',$startExam->exam_id)->first()->subject_id;
         //dd($subjectid);   
        if(Cache::has($cache_id)) {
          $questionList = Cache::get($cache_id);
          //dd($value);
        }else{
          return response()->json(['status'=>0,'message'=>'No Exam exist with this id','data'=>json_decode("{}")]);                    
        }
        $total_question = $questionList->count();
        
        if(isset($request->data) && count($request->data)>0){
          if(count($request->data) != $questionList->count()){
            return response()->json(['status'=>0,'message'=>'Question list mismatch','data'=>json_decode("{}")]);                    
          }
          $marks = 0;
          $finalQuestion = [];
          foreach($request->data as $k=>$v){
            
            foreach($questionList as $k1=>$v1){
              if($v1->id==$v['Qid']){
                $optionData = \App\Option::where(['id'=>$v['id'],'question_id'=>$v['Qid']])->first();
                
                if(isset($optionData->id) && $optionData->is_correct=='Y'){ 
                  // $arr = array('Qid'=>$v['Qid'],'option_id'=>$v['id'],'is_correct'=>'Y');                 
                  $is_correct = "Y";
                  
                  $marks++;
                } else {
                  // $request->data[$k]['is_correct'] = 'N';
                  
                  $is_correct = "N";
                }
                $arr = array('Qid'=>$v['Qid'],'option_id'=>$v['id'],'is_correct'=>$is_correct);
                $result = new \App\ExamResult();
                $result->user_id = $user->id;
                $result->exam_id = $exam_id;
                $result->question_id = $v['Qid'] ;
                $result->option_id = \App\Option::where(['is_correct'=>'Y','question_id'=>$v['Qid']])->first()->id;
                $result->ans_id = $v['id'];
                $result->is_correct = $is_correct;
                $result->subject_id = $subjectid;
                if($is_correct == 'Y')
                {
                  $result->marks = '1';
                }
                if($v['IsSkipped'] == 'Y') 
                {  
                  $result->is_attempted ='Skip'; 
                }
                else{
                  if($v['IsAttempted'] == 'Y') { $result->is_attempted = 'Yes';  }if($v['IsAttempted'] == 'N') {  $result->is_attempted = 'No'; }
                }
                
                $result->save();
                // \App\ExamResult::insert(['user_id'=>'1','exam_id'=>'1','question_id'=>'1','option_id'=>'1','question_id'=>'1']);
                $finalQuestion[$k] = $arr;
              }
            }
          }
          

          
          $examList = \App\Exam::select('class_id','subject_id')->where('id',$startExam->exam_id)->first();
          $history = new \App\HistoryUser();
          $history->exam_id = $startExam->id;
          $history->exam_date = date('Y-m-d H:i:s');
          $history->class_id = $examList->class_id;
          // $history->subject_id = $examList->subject_id;
          $history->rank_id = '1';
          $history->user_id = $user->id;

          $history->save();
          $percentage = ($marks*100)/$total_question;
          if($percentage < 40)
          {
            
          $startExam->result = 'Fail';
          }
          else{
            $startExam->result = 'Pass';
            $this->global_rank($user->id);
            
            
            // die();
            // $leaderBoard = new \App\Leaderboard();
            // $leaderBoard->user_id = $user->id;
            // $leaderBoard->subject_id = \App\Exam::where(['id'=>$startExam->exam_id])->first()->subject_id;
            // $leaderBoard->rank = '1';
            // $leaderBoard->points = $marks;
            // $leaderBoard->subject_rank = '1';
            // $leaderBoard->subject_points = $marks;
            // $leaderBoard->save();

          }
          $startExam->status = 'Completed';
          $startExam->marks = $marks;
          $startExam->end_timer = date('Y-m-d H:i:s');
          $startExam->percentage = $percentage;

          $resultData = ['questions'=>$request->data,'result'=>$startExam];

          if($startExam->save()){
	        $diviceIds = [$user->device_token]; 
      // return response()->json(['status'=>1,'message'=>'check status run ','data'=>$exam_id]);
	    if($diviceIds){
                $suscription_title = "Exam Result";
                $suscription_msg = "Your Exam has been done and result will display";
                // $this->sendNotification($diviceIds,'',$suscription_title,$suscription_msg);
                // Mail::to($user->email)->send(new FinishExamMail(['result'=>$startExam,'user'=>$user]));
            }
            return response()->json(['status'=>1,'message'=>'','data'=>$resultData]);                    
          }else{
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);                    
          }

          
        }           

      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }

    public function examsList(Request $request)
    {
      try{
          $user = JWTAuth::user();
          if(!isset($user->id)){
            return response()->json(['status'=>0,'message'=>'Invalid Token','data'=>json_decode("{}")]);
          }
          $result = \App\ExamResult::where(['user_id'=>$user->id])->orderBy('id','DESC')->groupBy('exam_id')->get();	  
          if($result){
            return response()->json(['status'=>0,'message'=>'','data'=>$result]);                    
          }else{
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);                    
          }

      }
      catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }
    public function examsResult(Request $request)
    {
      // return $request->exam_id;
      try{
        $user = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
        }
        $validator = Validator::make($request->all(), [
          'exam_id' => 'required'                           
        ]);
        $params = [];  
        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->exam_id[0])){
            $message = $error->exam_id[0];
          }
          return response()->json(["status"=>0,"message"=>$message,"data"=>json_decode("{}")]);
        }
        $details = \App\StartExam::select('exams.title')->join('exams','exams.id','=','start_exam.exam_id')->where('start_exam.id',$request->exam_id)->first();
        $result = \App\ExamResult::where(['user_id'=>$user->id,'exam_id'=>$request->exam_id])->orderBy('id','DESC')->get();
        $skip = 0;
        $wrong = 0;
        $correct = 0;
        foreach($result as $val)
        {
          if($val->is_correct == 'Y')
          {
            $correct++;
          }
          if($val->is_correct == 'N' && $val->is_attempted == 'Yes')
          {
            $wrong++;
          }
          if($val->is_attempted == 'Skip')
          {
            $skip++;
          }

        }
        if($correct > 0)
        {
          $correct = $correct.'('.round($correct*100/count($result),2).'%)';
        }
        else{
          $correct = '0(0%)';
        }
        if($wrong > 0)
        {
          $wrong=  $wrong.'('.round($wrong*100/count($result),2).'%)';
        }
        else{
          $wrong = '0(0%)';
        }
        if($skip > 0)
        {
          $skip = $skip.'('.round($skip*100/count($result),2).'%)';
        }
        else{
          $skip = '0(0%)';
        }
        $arr = array('Correct Answer'=>$correct,'Wrong Answer'=>$wrong,'Skip Answer'=>$skip);
        $arr1 = array('Exam Details'=>$details,'Result'=>$arr);
        if($arr1){
          return response()->json(['status'=>1,'message'=>'','data'=>$arr1]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);                    
        }

    }
    catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
    }
  
    }
    public function answersheet(Request $request)
    {
      try{
          // $tr = new GoogleTranslate();
          // $lang = $request->language;
          // $tr->setTarget($lang);
          $user = JWTAuth::user();
          if(!isset($user->id)){
            return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
          }
          $validator = Validator::make($request->all(), [
            'exam_id' => 'required'                           
          ]);
          $params = [];  
          if($validator->fails()){
            $error = json_decode(json_encode($validator->errors()));
            if(isset($error->exam_id[0])){
              $message = $error->exam_id[0];
            }
            return response()->json(["status"=>0,"message"=>$message,"data"=>json_decode("{}")]);
          }

          $result = \App\ExamResult::select('exam_results.*','alloption.question_hindi')
                                  ->join('questions as alloption','alloption.id','=','exam_results.question_id')
                                  ->where(['user_id'=>$user->id,'exam_id'=>$request->exam_id])
                                    ->get();
           //dd($result);                       
	$correct= 'Y';
          for($i=0; $i<count($result);$i++)
          {
            $result[$i]->options = \App\Option::select('id','option_hindi as option_name','is_correct as correct_ans')
                                                ->where('question_id',$result[$i]->question_id)->get();
	    $correct_answer = \App\Option::where('question_id',$result[$i]->question_id)->where('is_correct',$correct)->first();
	    $result[$i]->correct_answer = $correct_answer->option_name;
          }
	//dd($result);
          if($result){
            return response()->json(['status'=>1,'message'=>'Answersheet Data','data'=>$result]);                    
          }else{
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);                    
          }

      }
      catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }

    public function examHistrory(Request $request)
    {
      try{
        $user = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid TOken','data'=>json_decode("{}")]);
        }
        $result = \App\HistoryUser::select('history_users.*','exams.title','start_exam.percentage','start_exam.marks')
                                    ->join('start_exam','start_exam.id','=','history_users.exam_id')
                                    ->join('exams','exams.id','=','start_exam.exam_id')
                                    ->where(['history_users.user_id'=>$user->id])->get();

        if($result){
          return response()->json(['status'=>0,'message'=>'','data'=>$result]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);                    
        }

    }
    catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
    }
    }


    public function global_rank($user_id)
    {
      $class_id = \App\UserProfile::select('user_profiles.class_id')->where("user_id",$user_id)->first()->class_id;
      $subjects = \App\Subject::where("class_id",$class_id)->get();
      for($i=0;$i<count($subjects);$i++)
      {
        
        $exam = \App\Exam::where('subject_id',$subjects[$i]->id)->first();
        
        $leaderBoard = \App\Leaderboard::where(['subject_id'=>$subjects[$i]->id,'class_id'=>$class_id])->first();
        // dd($leaderBoard->id);
        if(!$leaderBoard)
        {
          
          $marks1 = \App\ExamResult::where('subject_id',$subjects[$i]->id)->sum('marks');
          $leaderBoardSubject = new \App\Leaderboard();
          $leaderBoardSubject->user_id = $user_id;
          $leaderBoardSubject->subject_id = $subjects[$i]->id;
          $leaderBoardSubject->class_id = $class_id;
          if($exam)
          {
            
            $leaderBoardSubject->subject_points = ($marks1*100/$exam->max_marks)/10;
          }
          else{
            // $leaderBoardSubject->subject_rank = 0;
            $leaderBoardSubject->subject_points =0;
          }
          $leaderBoardSubject->subject_rank = 0;
          // dd($leaderBoardSubject);
          $leaderBoardSubject->save();
          $this->getRank($user_id,$subjects[$i]->id,$class_id,$leaderBoardSubject->subject_points);
          $this->getRankTotal($user_id,$subjects[$i]->id,$class_id,'');
        }
        else{
          
          $marks1 = \App\ExamResult::where('subject_id',$subjects[$i]->id)->sum('marks');
          
          $leaderBoardSubject = \App\Leaderboard::findOrFail($leaderBoard->id);
          $leaderBoardSubject->user_id = $user_id;
          $leaderBoardSubject->subject_id = $subjects[$i]->id;
          $leaderBoardSubject->class_id = $class_id;
          // $leaderBoardSubject->point = '1';
          // $leaderBoardSubject->rank = '1';
          if($exam)
          {
            // print($marks1*100/$exam->max_marks);
            // $leaderBoardSubject->subject_rank = '';
            $leaderBoardSubject->subject_points = ($marks1*100/$exam->max_marks)/10;
          }
          else{
            // $leaderBoardSubject->subject_rank = 0;
            $leaderBoardSubject->subject_points =0;
          }
          $leaderBoardSubject->subject_rank = 0;
          
          
          // dd($leaderBoardSubject);
          $leaderBoardSubject->save();
          $this->getRank($user_id,$subjects[$i]->id,$class_id,$leaderBoardSubject->subject_points);
          $this->getRankTotal($user_id,$subjects[$i]->id,$class_id,'');
        }
      }
      $startExam = \App\StartExam::where(['user_id'=>$user_id,'status'=>'Completed'])->get();
      // dd($startExam);
    }

    public function getRank($userid,$subject,$class,$points)
    {
      if($points > 0)
      {
        
       
      $subjectPoints = \App\Leaderboard::where(['subject_id'=>$subject,'class_id'=>$class,'user_id'=>$userid])->pluck('subject_points')->toArray();
      
      rsort($subjectPoints);
      // dd($subjectPoints);
      $arr = $arr1 = array();
        $total = 0;
      foreach ($subjectPoints as $key => $value) {
          $total += $value;
          $arr[$value][] = $value;
      }

      $i = $j = 1;

      foreach ($arr as $key => $value) {
          
          foreach ($value as $key1 => $value1) {
            // dd($key,$points);
            if( $key == $points)
            {
              \App\Leaderboard::where(['user_id'=>$userid,'subject_id'=>$subject,'class_id'=>$class])->update(['subject_rank'=>$i]);
              // dd($i);
              
            }
              // 
              $j++;
          }
      $i = $j;

      
      }
      
    }
    return 0;
    }
    public function getRankTotal($userid,$subject,$class,$points)
    {
      
      $subjectPoints = \App\Leaderboard::where(['user_id'=>$userid])->sum('subject_points');
      $check = \App\GlobalLeaderboard::where('user_id',$userid)->first();
      $all = \App\GlobalLeaderboard::whereNotIn('user_id',array($userid))->pluck('points')->toArray();
     
      if($all)
      {
        \App\GlobalLeaderboard::insert(['user_id'=>$userid,'points'=>$subjectPoints]);
       $subjectPoint = array("$subjectPoints");
      //  dd(array_merge($all,$subjectPoint));
        $data = array_merge($subjectPoint,$all);
        
      }
      else{
        \App\GlobalLeaderboard::where(['user_id'=>$userid])->update(['points'=>$subjectPoints]);
        $data = array($subjectPoints);
      }
      
      rsort($data);
      // dd($subjectPoints);
      $arr = $arr1 = array();
        $total = 0;
      foreach ($data as $key => $value) {
          $total += $value;
          $arr[$value][] = $value;
      }
      
      $i = $j = 1;

      foreach ($arr as $key => $value) {
          
          foreach ($value as $key1 => $value1) {
            // dd($key,$points);
            if( $key == $subjectPoints)
            {
              if(!$check)
              {
                $leaderboard = \App\GlobalLeaderboard::insert(['user_id'=>$userid,'rank'=>$i,'points'=>$subjectPoints]);
                // $leaderboard->user_id = $userid;
                // $leaderboard->rank = $i;
                // $leaderboard->points = $subjectPoints;
                // $leaderboard->save();
              }
              else{
                $leaderboard = \App\GlobalLeaderboard::where(['user_id'=>$userid])->update(['rank'=>$i]);
              }
              
              // dd($i);
              
            }
              // 
              $j++;
          }
      $i = $j;

      
      
    }
    return 0;
    }


    public function checkExamExist(Request $request)
    {
      // dd($request->all());
        try{
          $user = JWTAuth::user();
          if(!isset($user->id)){
            return response()->json(['status'=>0,'message'=>'Invalid Token','data'=>json_decode("{}")]);
          }

          
          $type = $request->type;
          if($type == 'chapter')
          {
            $exam = \App\Exam::select('exams.id','exams.description','subjects.subject_name','chapters.chapter_name');
            $chapter_id = $request->chapter_id;
            $subject_id = $request->subject_id;
            $exam->join('chapters','chapters.id','=','exams.chapter_id')->join('subjects','subjects.id','=','exams.subject_id')->where(['chapter_id'=>$chapter_id,'exams.subject_id'=>$subject_id,'exam_type'=>'Chapter']);
          }
          else{
            $exam = \App\Exam::select('exams.id','exams.description','subjects.subject_name');
            $subject_id = $request->subject_id;
            $exam->join('subjects','subjects.id','=','exams.subject_id')->where(['exams.subject_id'=>$subject_id,'exam_type'=>'Subject']);
          }
          $exam = $exam->first();
          if($exam){
            return response()->json(['status'=>1,'message'=>'','data'=>$exam]);                    
          }else{
            return response()->json(['status'=>0,'message'=>'Exam not found.','data'=>json_decode("{}")]);                    
          }
        }catch(Exception $e){
          return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
        }
    }
    public function subjectWiseRank(Request $request)
    {
      try{
        $user = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid Token','data'=>json_decode("{}")]);
        }

        // $topclaims = \App\Leaderboard::orderByRaw("CAST(subject_rank as UNSIGNED) DESC")->limit(3)->get(); 
        // dd($topclaims);
        $class = $request->class_id;
        $subject = $request->subject_id;
	$result = \App\Leaderboard::join('users','users.id','leaderboards.user_id')->join('subjects','subjects.id','leaderboards.subject_id')->select('users.name','subjects.subject_name','leaderboards.subject_rank','leaderboards.subject_points')->where(['leaderboards.subject_id'=>$subject,'leaderboards.class_id'=>$class])->groupBy('leaderboards.user_id')->orderBy('leaderboards.subject_rank', 'asc')->offset(0)->limit(5)->get();
        if(count($result) > 0){
          return response()->json(['status'=>1,'message'=>'Subject Wise Rank Data','data'=>$result]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'Data not found.','data'=>json_decode("{}")]);                    
        }
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
  
    }
    public function globalRank(Request $request)
    {
      try{
        $user = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid Token','data'=>json_decode("{}")]);
        }

        //$result = \App\GlobalLeaderboard::select('rank','points')->where(['user_id'=>$user->id])->get();
	$result = \App\GlobalLeaderboard::join('users','users.id','global_leaderboards.user_id')->select('users.name','global_leaderboards.rank',DB::raw('SUM(points) as points'),'users.image')
->groupBy('users.id')
->orderBy('global_leaderboards.rank', 'asc')->get();
	$resultFinal = $result->toArray();

	array_multisort(array_map(function($element) {
      		return $element['points'];
  	}, $resultFinal), SORT_DESC, $resultFinal);

	for($i=0; $i<count($resultFinal);$i++){
		$resultFinal[$i]['rank'] = $i+1;
		$resultFinal[$i]['points'] = round($resultFinal[$i]['points'], 2);
	}

	//dd($resultFinal);
        if(count($result) > 0){
          return response()->json(['status'=>1,'message'=>'Global Rank Data','data'=>$resultFinal]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'Data not found.','data'=>json_decode("{}")]);                    
        }
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }

    public function userRank(Request $request)
    {
      try{
        $user = JWTAuth::user();
        if(!isset($user->id)){
          return response()->json(['status'=>0,'message'=>'Invalid Token','data'=>json_decode("{}")]);
        }
	
	$check = \App\GlobalLeaderboard::select('user_id',DB::raw('SUM(points) as points'))
	->orderBy('points','desc')->groupBy('user_id')->get();
	$countRow = 0;$total_rank = 0;
	foreach($check as $val)
	{
		$countRow++;
		if($val->user_id == $user->id)
		{ $total_rank = $countRow; }
	}
	
	//dd($user->id,$countRow1,$check);
	$result = \App\GlobalLeaderboard::join('users','users.id','global_leaderboards.user_id')->select('users.name','global_leaderboards.rank','global_leaderboards.points')->where(['user_id'=>$user->id])->orderBy('global_leaderboards.rank', 'asc')->get();
	$totalExam = count($result);
	$total_point=0;
	foreach($result as $value){
		$total_point += $value->points;
	}
	
        if(count($result) > 0){
          return response()->json(['status'=>1,'message'=>'User Rank Data','name' => $result[0]->name,'total_exam_count'=>$totalExam,'total_points'=>round($total_point,2), 'total_rank' => $total_rank]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'Data not found.','total_exam_count'=>'','total_points'=>'', 'total_rank' => '']);                    
        }
      }catch(Exception $e){
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
    }

    public function testmail()
    {
	//Mail::raw('Text to e-mail', function($message)
	//{
         //$message->from('nitesh182185048@gmail.com', 'Laravel');
         //$message->to('nitesh182185048@gmail.com');
	//});
	Mail::to('nitesh182185048@gmail.com')->send(new RegistrationMail('hello'));
      //Mail::to('yogesh.sharma@techconfer.in')->send(new RegistrationMail('hello'));
    }

    public function chanperWiseExam(Request $request)
    {
      $params = [
        'subject_id'=>$request->subject_id,
        'class_id'=>$request->class_id,
        'chapter_id'=>explode(',',$request->chapter_id),
        'language'=>$request->language
      ];
      $questionList = \App\Question::getRandomQuestion($params);
      // dd($questionList);
      return response()->json(['status'=>1,'message'=>'Exam Data','data'=>[
        'questions'=>$questionList
      ]]); 
    }


}