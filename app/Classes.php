<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'class_name','status'
    ];

    public static function getAllClasses($params){
        
        $result = Self::where('id','<>',0);
                
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function deleteClass($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getClassDD(){
        return Self::where('id','<>',0)->pluck('class_name','id')->sortBy('class_name');
    }

    public static function getSubjectDD(){
        return Self::where('status','Y')->pluck('class_name','id')->sortBy('class_name');
    }

    public static function classNameIdArray($classObj){
        $classArr = [];
        if($classObj->count()){
            foreach($classObj as $k=>$v){
                $classArr[$v->class_name] = $v->id;
            }
        }
        return $classArr;
    }
}
