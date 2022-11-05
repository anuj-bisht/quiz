<?php

namespace App\Http\Controllers\v1;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Traits\SendMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Config;
use Mail;
use App\User;
use App\Order;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SendMail;

    public $paging = 10;
    /**
   * 
   * Notify mail sent */
  public function sendNotificationMail($userData){
    
    //email sent code for event creator
    //$userData = User::where('email',$email)->first();
    $app = app();
    $data = $app->make('stdClass');
    
    $data->type = $userData->type;//$userData->type;
    $data->city_id = $userData->city_id;
    $data->state_id = $userData->state_id;
    $data->country_id = $userData->country_id;
    $data->country_name =  $userData->Country->name; 
    $data->state_name =  $userData->State->name; 
    $data->city_name =  $userData->City->name; 
    $notifyObj = new NotifyMe();    
    $notifyList = $notifyObj->getNotifiedDataByType($data);
    
    if($notifyList->count() > 0){
        Log::info(['add '.$data->type.' notify data found',$notifyList]);
        $mailArr = [];
        $ids = [];
        foreach($notifyList as $k=>$v){
            if(!in_array($v->mail,$mailArr)){                            
                array_push($mailArr,$this->encdesc($v->email,'decrypt'));
                array_push($ids,$v->id);
                //$ids[] = $v->id;
            }                                                   
        }                              
        $maildata['email'] = $mailArr;
        $maildata['name'] = $userData->name;
        $maildata['city_name'] = $data->city_name;
        $maildata['type'] = ucfirst($data->type);
        $maildata['subject'] = ucfirst($data->type). ' Add Notification From '.config('app.site_name');
        $maildata['supportEmail'] = config('mail.supportEmail');
        $maildata['website'] = config('app.site_url');  
        $maildata['site_name'] = config('app.site_name');  
      
        if($this->SendMail($maildata,'notify_while_add')){
            NotifyMe::whereIn('id', $ids)->update(['notified' => 1]);
            Log::info(['add user notify mail sent']);
            return true;
        } 
    }
    return true;
    
  }

  public function encdesc($stringVal,$type='encrypt'){
      
    $stringVal = str_replace("__","/",$stringVal);  
    if($type=='encrypt'){
        return openssl_encrypt($stringVal,"AES-128-ECB",'Xz!Y2zRR4567!#$!');
    }else{
        return openssl_decrypt($stringVal,"AES-128-ECB",'Xz!Y2zRR4567!#$!');
    }        
  }

  public function datediffs($start,$end,$format=""){
    

    $seconds = strtotime($start) - strtotime($end);
    $hours = $seconds / 60 /  60;    
    return $hours;

  }

  public function datecompare($start,$end){
    
    if($start > $end){
        return true;
    }else{
        return false;
    }

  }

  public function getGoalWeightProgress($user_id){
    $data = User::getGoalWeightProgress($user_id);
    return $data;
  }


  public function confirmOrder($order_id,$subscription_id=0){
    $order = Order::findOrFail($order_id);
    if(isset($order->id)){
      $order->status = 'Completed';
      if($subscription_id){
        $order->subscription_id = $subscription_id;
      }
      $order->save();
      return true;
    }
    return false;
  }

}