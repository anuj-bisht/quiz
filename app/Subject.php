<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_name','subject_banner', 'subject_logo','class_id','status',
    ];

    public function classes()
    {
        return $this->belongsTo('App\Classes','class_id');
    }

    public static function getAllSubject($params){
        
        $result = Self::where('id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function deleteSubject($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getSubjectDD(){
        return Self::where('status','Y')->pluck('subject_name','id')->sortBy('subject_name');
    }

    public static function filterResult($obj){
        foreach($obj as $k=>$v){
            $obj[$k]->class_name = $v->classes->class_name;            
        }
        return $obj;
    }

    public static function getSubjectByClass($params){
        
        $result = Self::where('class_id',$params['class_id']);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function getSubjectDDByClass($params){        
        return Self::where('status','Y')->where('class_id',$params['class_id'])->pluck('subject_name','id')->sortBy('subject_name');                
    }


    public static function subjectNameIdArray($obj){
        $arr = [];
        if($obj->count()){ 
            foreach($obj as $k=>$v){
                $arr[$v->subject_name] = $v->id;
            }
        }
        return $arr;
    }

    
}
