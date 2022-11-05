<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_id', 'block_code','block_name','status',
    ];

    public function district()
    {
        return $this->belongsTo('App\District');
    }

    public static function getAllBlock($params){
        
        $result = Self::where('id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function ajaxGetBlockByDistrict($params){
        
        $result = Self::where('district_id',$params['district_id']);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function ajaxGetBlockByDistrictDD($params){
        
        $result = Self::where('district_id',$params['district_id']);
        
        if($params['status']=='Y'){
            $result = $result->where('status','Y');
        }
        if($params['get_result']){
            $result = $result->pluck('block_name','id')->sortBy('name');
        }
        
        return $result;
    }

    public static function deleteBlock($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getBlockDD(){
        return Self::where('status','Y')->pluck('block_name','id')->sortBy('block_name');
    }

    public static function blockNameIdArray($obj){
        $objArr = [];
        if($obj->count()){
            foreach($obj as $k=>$v){
                $objArr[$v->block_code] = $v->id;
            }
        }
        return $objArr;
    }
}
