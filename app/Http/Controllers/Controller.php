<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Traits\SendMail;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\View;
use PDF;
use App\LevelUser;
use App\Subscription;
use App\Menu;
use App\Plan;
use App\Slot;
use App\Order;
use App\Schedule;
use DB;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SendMail, Common;
    
    public function __construct(){
        $menu = array(
            'items' => array(),
            'parents' => array()
        );
        $obj = Menu::where('id','<>', '')->get();

        foreach($obj as $k=>$items){
            $menu['items'][$items->id] = $items;
            $menu['parents'][$items->parent_id][] = $items->id;
        }
        View::share('menuData', $menu);
       //echo '<pre>'; print_r($menu);die;
    }
    
    public $ajaxResponse = ["success"=>false,"msg"=>"","data"=>[]];

    public $chainage_gap = 100;
    public $paging = 10;

    
    

    
    public function getStatesByCountry(Request $request){
        try{
            
            $result = State::getStateByCountry($request->id);                                    
            return response()->json(['status'=>1,'message'=>'','data'=>$result]);    
        }catch(Exception $e){			
            return response()->json(['status'=>0,'message'=>'Error','data'=>json_decode("{}")]);    
        }
    }

    
    
    public function updateOrder($order_id){
        $order = Order::findOrFail($order_id);
        if(isset($order->id)){
            $order->scheduled = 'Y';
            $order->save();
            return true;
        }
        return false;
    }


    public function convertToMilisecond($timestr){
        //$string = "00:11.546";
        
        
        $time   = explode(":", $timestr);
        //$hour   = $time[0] * 60 * 60 * 1000;
        $minute = (int)$time[0]*60*1000;
        //$mil = explode(".",$time[1]);
        $sec    = (float)$time[1]*1000;
        //$mls = (int)$mil[1];
        $result = $minute + $sec;
        return $result;
    }

    public function convertToTime($input){
        //$input = 4648;
        $uSec = $input % 1000;
        $input = floor($input / 1000);
        $seconds = $input % 60;
        $input = floor($input / 60);
        $minutes = $input % 60;
        $input = floor($input / 60);
        $hour = $input ;
        return sprintf('%02d:%02d:%03d', $minutes, $seconds, $uSec);
    }


    public function subscriptionReminder(Request $request){

        $subs = Subscription::getSubsDue();        
        if($subs->count()){
            foreach($subs as $k=>$v){
                $data = [];
                $data['to_email'] = $v->user_email;      
                $data['from'] = config('app.MAIL_FROM_ADDRESS');                  
                $data['subject'] = 'Subscription end reminder';
                $data['name'] = $v->username;
                $data['message1'] = 'Your subscription is expiring soon. Please make payment asap to continue.';
                $this->SendMail($data,'subsreminder'); 
            }

            echo "subscription reminder sent";
            
        }
        
    }

    public function lastreminder(Request $request){

        $minute = 15;
        $diviceIds = [];
        $subs = Schedule::getReminderByMinute($minute,'schedule');        
        $tests = Schedule::getReminderByMinute($minute,'test');                
        $demos = Schedule::getReminderByMinute($minute,'demo');        
        //dd($demos);
        //dd($subs);
        if($subs->count()){
            foreach($subs as $k=>$v){
                $data = [];
                $data['to_email'] = $v->user_email;      
                $data['from'] = config('app.MAIL_FROM_ADDRESS');                  
                $data['subject'] = 'Class Reminder';                
                if(isset($v->trainer_email)){
                    $data['bcc'] = $v->trainer_email; 
                }                
                $data['message1'] = 'Your livefit training class is just going to start in few minutes, Please be ready and see schedule in mobile';
                $data['name'] = $v->username;

                $data['start_time'] = $v->start;                
                $data['category_name'] = $v->category_name;
                $data['trainer_name'] = $v->trainer_name;

                if(isset($v->device_id)){
                    $diviceIds[] = $v->device_id;
                }
                
                $this->SendMail($data,'schedule_reminder'); 
            }            
            
        }

        if($tests->count()){
            foreach($tests as $k=>$v){
                $data = [];
                $data['to_email'] = $v->user_email;      
                $data['from'] = config('app.MAIL_FROM_ADDRESS');                  
                $data['subject'] = 'Test Class Reminder';                
                $data['message1'] = 'Your livefit training assesment is just going to start in few minutes, Please be ready and see schedule in mobile';
                $data['name'] = $v->username;
                if(isset($v->trainer_email)){
                    $data['bcc'] = $v->trainer_email; 
                }
                $data['start_time'] = $v->assign_slot;                
                $data['category_name'] = $v->level_name;
                $data['trainer_name'] = $v->trainer_name;
                
                if(isset($v->device_id)){
                    $diviceIds[] = $v->device_id;
                }
                $this->SendMail($data,'schedule_reminder'); 
            }            
            
        }

        if($demos->count()){
            foreach($demos as $k=>$v){
                $data = [];
                $data['to_email'] = $v->user_email;      
                $data['from'] = config('app.MAIL_FROM_ADDRESS');                  
                $data['subject'] = 'Demo Class Reminder';                
                $data['message1'] = 'Your livefit demo class is just going to start in few minutes, Please be ready and see schedule in mobile';
                $data['name'] = $v->username;
                if(isset($v->trainer_email)){
                    $data['bcc'] = $v->trainer_email; 
                }
                $data['start_time'] = $v->assign_slot;                
                $data['category_name'] = $v->category_name;
                $data['trainer_name'] = $v->trainer_name;
                
                if(isset($v->device_id)){
                    $diviceIds[] = $v->device_id;
                }

                $this->SendMail($data,'schedule_reminder'); 
            }            
            
        }

        if(count($diviceIds)){
            $this->sendNotification($diviceIds,'','Schedule Reminder','Your class is just about to start, Please join asap');
        }


        echo "reminder sent";
        
    }


    public function crontest(){
        

        $myfile = fopen("/var/www/html/quizs/public/uploads/crontest_".rand(100,1000).".txt", "w") or die("Unable to open file!");
        $txt = "John Doe\n";
        fwrite($myfile, $txt);
        $txt = "Jane Doe\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    
}
