<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Topic extends Model
{
    protected $fillable = [
        'chapter_id','class_id', 'subject_id','topic_name','status'
    ];

    public function chapter()
    {
        return $this->belongsTo('App\Chapter');
    }

    public function class()
    {
        return $this->belongsTo('App\Classes');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    public static function getAllTopic($params){
        
        $result = Self::where('id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function deleteTopic($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getTopicDD(){
        return Self::where('status','Y')->pluck('topic_name','id')->sortBy('topic_name');
    }
}
