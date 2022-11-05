<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Chapter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id','subject_id', 'chapter_name','chapter_name_hindi','status'
    ];

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }
    
    public function classes()
    {
        return $this->belongsTo('App\Classes','class_id');
    }

    

    public static function getAllChapter($params){
        
        $result = Self::where('id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function deleteChapter($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getChapterDD(){
        return Self::where('status','Y')->pluck('chapter_name','id')->sortBy('chapter_name');
    }

    public static function getChapterBySubject($params){
 
	if(isset($params['language'])){
		if($params['language'] =="hi"){
        $result = Self::select('id','chapter_name_hindi as chapter_name','chapter_name_hindi','subject_id','class_id','status','created_at','updated_at','deleted_at')->where('subject_id',$params['subject_id']);

        }elseif($params['language'] =="en"){

	$result = Self::select('id','chapter_name','chapter_name_hindi','subject_id','class_id','status','created_at','updated_at','deleted_at')->where('subject_id',$params['subject_id']);
	}
	}else{

	$result = Self::select('id','chapter_name_hindi as chapter_name','subject_id','class_id','status','created_at','updated_at','deleted_at')->where('subject_id',$params['subject_id']);
	}
	
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function filterResult($obj){
        foreach($obj as $k=>$v){
            $obj[$k]->class_name = $v->classes->class_name;            
            $obj[$k]->subject_name = $v->subject->subject_name;            
        }
        return $obj;
    }

    public static function getChapterDDBySubject($params){
        
        return Self::where('status','Y')->where('subject_id',$params['subject_id'])->pluck('chapter_name_hindi','id')->sortBy('chapter_name_hindi');        
        
    }

    public static function getChapterByProfile($userProfileObj){
        
        $result = Self::where('class_id',$userProfileObj->class_id)->get();
        $result = self::filterResult($result);
        return $result;
    }

    public static function chapterNameIdArray($obj){
        $arr = [];
        if($obj->count()){
            foreach($obj as $k=>$v){
                $arr[$v->chapter_name_hindi] = $v->id;
            }
        }
        return $arr;
    }
}
