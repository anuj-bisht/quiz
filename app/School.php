<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_id', 'school_code','school_name','status','address','pincode'
    ];

    // public function block()
    // {
    //     return $this->belongsTo('App\Block');
    // }

    public static function getAllSchool($params){
        
        $result = Self::select('schools.*','districts.district_name')
                        ->join('districts','districts.id','=','schools.district_id')
			//->where('schools.district_id','=',$params['district_id']);
                        ->where('schools.id','<>',0);
        
        if($params['status']=='Y'){
            $result = $result->where('schools.status','Y');
        }
        // if($params['block_id'] !=''){
        //     $result = $result->where('block_id',$params['block_id']);
        // }
        if($params['get_result']){
            $result = $result->get();
        }
        
        return $result;
    }

    public static function deleteSchool($id){
        
        $result = Self::where('id',$id)->delete();        

        return $result;
    }

    public static function getSchoolDD(){
        return Self::where('status','Y')->pluck('school_name','id')->sortBy('school_name');
    }
}
