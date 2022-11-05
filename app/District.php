<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_name', 'district_code','status',
    ];

    public static function getAllDistrict($params){
        
        $result = Self::where('id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function deleteDistrict($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getDistricDD(){
        return Self::where('status','Y')->pluck('district_name','id')->sortBy('name');
    }

    public static function districtNameIdArray($obj){
        $objArr = [];
        if($obj->count()){
            foreach($obj as $k=>$v){
                $objArr[$v->district_code] = $v->id;
            }
        }
	//dd($objArr);
        return $objArr;
    }
}
