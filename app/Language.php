<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';
    protected $fillable = ['lang_code','language_name'];
    

    public static function getAllList(){
        return Self::pluck('language_name','id')->sortBy('language_name');
    }  

   
    public static function getAllLanguage($params){
        
        $result = Self::where('id','<>',0);
                
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function languageNameIdArray($obj){
        $arr = [];
        if($obj->count()){
            foreach($obj as $k=>$v){
                $arr[$v->language_name] = $v->id;
            }
        }
        return $arr;
    }

}
