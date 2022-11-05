<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Question extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id','subject_id','language_id','chapter_id','status',
        'question','question_hindi','image','file_path','is_image'
    ];

    public function classes()
    {
        return $this->belongsTo('App\Classes','class_id');
    }


    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    public function language()
    {
        return $this->belongsTo('App\Language');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Chapter');
    }

    public function option()
    {
        return $this->hasMany('App\Option');
    }

    public static function getAllQuestion($params){
        
        $result = Self::where('id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function deleteQuestion($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getRandomQuestion($params){ 
	if(isset($params['language'])){
		if($params['language'] =="hi"){
        $result = Self::with('Option')->select('questions.id','questions.class_id','questions.subject_id','questions.chapter_id','questions.language_id','questions.lang_convert','questions.question_hindi as question','questions.question_hindi','questions.image','questions.file_path','questions.is_image','questions.status','questions.created_at','questions.updated_at','questions.deleted_at')->where('class_id',$params['class_id'])->where('subject_id',$params['subject_id']);
		}elseif($params['language'] =="en"){
		$result = Self::with('Option')->where('class_id',$params['class_id'])->where('subject_id',$params['subject_id']);
		}
	}else{
	$result = Self::with('Option')->where('class_id',$params['class_id'])->where('subject_id',$params['subject_id']);
	}
        if($params['chapter_id']){
            $result = $result->whereIn('chapter_id',$params['chapter_id']);   
             
        }
        if(isset($params['no_of_question']))
        {
        $result = $result->inRandomOrder()->limit($params['no_of_question'])->get();

        }
        else{
        $result = $result->inRandomOrder()->get();

        }

        return $result;
    }
    
}
