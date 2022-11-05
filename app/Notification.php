<?php
  
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;  

class Notification extends Model
{
    

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','title','message','type'
    ];
    
        
    public static function getMyNotifications($user_id){
        $result = DB::table('notifications')
            ->select('notifications.*','users.id as user_id','users.name as username',
            'users.email')                                    
            ->join('users', 'users.id', '=', 'notifications.user_id')      
            ->where('notifications.user_id',$user_id)                                       
            ->get();
        return $result;        

    }

    public static function saveNotification($data=[]){
        $obj = new Notification();    
        $obj->title = (isset($data['title'])) ? $data['title']:'';
        $obj->user_id = (isset($data['user_id'])) ? $data['user_id']: 1;
        $obj->message = (isset($data['message'])) ? $data['message']: '';
        $obj->type = (isset($data['type'])) ? $data['type']: 'Normal';
        if($obj->save()){
            return true;
        }else{
            return false;
        }
    }
    
}
