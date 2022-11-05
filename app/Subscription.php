<?php
  
  namespace App;
  use Illuminate\Database\Eloquent\Model;
  use DB;
  
class Subscription extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','youtube_link'
    ];

    public static function getAllList(){
        return Self::all();
    }
	
	public static function getSubsByUser($user_id){
        return Self::select('subscriptions.*','users.name as username',
        'plans.name as plan_name','plans.price as plan_price',
        'plans.days as plan_days','plans.description as plan_description','categories.id as category_id',
        'categories.name as category_name')        
        ->join('users','users.id','=','subscriptions.user_id')
        ->join('plans','plans.id','=','subscriptions.plan_id')
        ->join('categories','categories.id','=','plans.category_id')        
        ->where('users.id',$user_id)
        ->where('subscriptions.status','active')        
        ->where(DB::raw('DATE(subscriptions.next_bill_date)'),'>=',date('Y-m-d'))        
        //->groupBy('plans.category_id')
        ->orderBy('subscriptions.id','DESC')
        ->get();
    }


    public static function getSubsByPlan($plan_id,$user_id){
        return Self::select('subscriptions.*','users.name as username',
        'plans.plan_name as plan_name','plans.plan_rate as plan_price',
        'plans.days as plan_days','plans.description as plan_description') 
	//return Self::select('subscriptions.*','users.name as username',
        //'plans.plan_name as plan_name','plans.plan_rate as plan_price',
        //'plans.days as plan_days','plans.description as plan_description',
        //'categories.id as category_id',
        //'categories.name as category_name')        
        ->join('users','users.id','=','subscriptions.user_id')
        ->join('plans','plans.id','=','subscriptions.plan_id')
        //->join('categories','categories.id','=','plans.category_id')        
        ->where('users.id',$user_id)
        ->where('subscriptions.plan_id',$plan_id)
        ->where('subscriptions.status','active')                                
        ->first();
    }

    public static function getSubsDue($remider_days=3){
        return Self::select('subscriptions.*','users.name as username','users.email as user_email',
        'plans.name as plan_name','plans.price as plan_price',
        'plans.days as plan_days','plans.description as plan_description',
        'categories.id as category_id',
        'categories.name as category_name')        
        ->join('users','users.id','=','subscriptions.user_id')
        ->join('plans','plans.id','=','subscriptions.plan_id')
        ->join('categories','categories.id','=','plans.category_id')                
        ->where('subscriptions.status','active')                                
        ->where(DB::raw('DATE(subscriptions.next_bill_date)'),'<=',date('Y-m-d', strtotime("+".$remider_days." day")))                                
        ->where(DB::raw('DATE(subscriptions.next_bill_date)'),'>=',date('Y-m-d'))                                
        //->where(DB::raw('DATE_ADD(subscriptions.next_bill_date,INTERVAL 1 DAY)'),'>=',date('Y-m-d'))                                
        ->get();
    }
    
}
