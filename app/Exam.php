<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id', 'subject_id','chapter_id','title','description',
        'status','no_of_question','exam_type'
    ];

    public function classes()
    {
        return $this->belongsTo('App\Classes');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Chapter');
    }

    public static function getAllExam($params){
        
        $result = Self::where('id','<>',0);
        
        if(isset($params['exam_type'])){
            $result = $result->where('exam_type',$params['exam_type']);
        }
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        

        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function getExamByUser($user_id){
        
        $result = UserProfile::where('user_id',$user_id)->get();                
                
        return $result;
    }

    public static function getExamById($exam_id){
        
        $result = Self::where('id',$exam_id)->first();                
                
        return $result;
    }

    public static function checkUserExamClass($user_id,$exam_id){ 
               
        $userdata = \App\UserProfile::getUserProfile($user_id);
        // dd($user_id);
        $exam_data = \App\Exam::getExamById($exam_id);

        if(isset($userdata->class_id) && isset($exam_data->class_id) && $userdata->class_id==$exam_data->class_id){
            return true;
        }
        return false;
    }
    

    public static function filterResult($obj){
        foreach($obj as $k=>$v){
            $obj[$k]->class_name = $v->chapter->subject->classes->class_name;                                
            $obj[$k]->subject_name = $v->subject->subject_name;            
            $obj[$k]->chapter_name = $v->chapter->chapter_name;            
        }
        return $obj;
    }

    public static function deleteExam($id){        
        $result = Self::where('id',$id)->delete();        
        return $result;
    }
    public static function getWhere($where)
    {
       
        $result = Self::where($where)->first();
        return $result;
    }

        
}
