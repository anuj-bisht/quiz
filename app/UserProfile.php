<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  use DB;
  
class UserProfile extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'default','user_id','class_id','school_id','district_id'
    ];
    

    public function class()
    {
        return $this->belongsTo('App\Classes');
    }

    public function district()
    {
        return $this->belongsTo('App\District');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function school()
    {
        return $this->belongsTo('App\School');
    }
	
	    
    public function language()
    {
        return $this->belongsTo('App\Language');
    }

    public static function filterResult($obj){
        foreach($obj as $k=>$v){            
            $obj[$k]->district_name = $v->district->district_name;                                
            $obj[$k]->school_name = $v->school->school_name;            
            //$obj[$k]->class_name = $v->classes->class_name;            
            $obj[$k]->user_name = $v->user->name;
        }
        return $obj;
    }

    public static function getUserProfile($user_id){
        $result = UserProfile::where('user_id',$user_id)
        ->where('default','Y')->first();        
        return $result;
    }
    
    
}
