<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';
    protected $fillable = ['plan_name','description','days','plan_rate','image','status'];
    //protected $hidden = ['_token'];



    public static function getAllList($params){
        $result = Self::where('id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }  


       
    
    public static function getPlanById($plan_id){
        
        return Self::select('plans.*')->where('plans.id',$plan_id)        
        ->first();
    }  

    
    
}
