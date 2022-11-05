<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['id','subscription_id','order_id','slot_id','razor_id','amount','txn_id','status','scheduled','user_id','currency','plan_id'];
    //protected $hidden = ['_token'];

    public static function getAllOrder(){
        return Self::all();
    }
            
    public static function getNewOrder($count){
        if($count){
            $result =  Order::select("orders.*",'users.name as username',
            'plans.name as plan_name')
            ->join('users','users.id','=','orders.user_id')
            ->join('plans','plans.id','=','orders.plan_id')
            ->where('orders.scheduled','N')->count();
        }else{
            $result =  Order::select("orders.*",'users.name as username','plans.name as plan_name')
            ->join('users','users.id','=','orders.user_id')
            ->join('plans','plans.id','=','orders.plan_id')
            ->where('orders.status','N')->where('orders.scheduled','N')->get();
        }
        return $result;
        
    }

    public static function getOrderById($order_id){
        
        $result  = Self::select('orders.*','slots.id as slot_id','slots.start_time as slot_start_time',
        'slots.end_time as slot_end_time','users.name as username',
        'subscriptions.id as subscription_id',
        'plans.id as plan_id','plans.days as plan_days','categories.id as category_id','categories.name as category_name')
        ->leftJoin('subscriptions','subscriptions.id','=','orders.subscription_id')
        ->join('plans','subscriptions.plan_id','=','plans.id')
        ->join('slots','slots.id','=','orders.slot_id')
        ->join('categories','plans.category_id','=','categories.id')
        ->join('users','users.id','=','orders.user_id')
        ->where('orders.id',$order_id)->first();

        return $result;
    }

    public static function getFailedOrder($count){
        if($count){
            $result =  Order::select("orders.*",'users.name as username',
            'plans.name as plan_name')
            ->join('users','users.id','=','orders.user_id')
            ->join('plans','plans.id','=','orders.plan_id')
            ->where('orders.subscription_id',0)->count();
        }else{
            $result =  Order::select("orders.*",'users.name as username','plans.name as plan_name')
            ->join('users','users.id','=','orders.user_id')
            ->join('plans','plans.id','=','orders.plan_id')
            ->where('orders.subscription_id',0)->get();
        }
        return $result;
    }
    
}
