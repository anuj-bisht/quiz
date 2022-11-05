<?php

namespace App\Http\Controllers\v1;

use App\Order;
use App\Subscription;
use App\Plan;
use App\LevelUser;
use App\Level;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Traits\SendMail;
use App\Http\Controllers\Traits\Common;
use Config;
use App\Common\Utility;
use Illuminate\Support\Facades\Http;
use App\Classes\UploadFile;
use Mail;
use Razorpay\Api\Api;
use App\Mail\SubscriptionMail;




class OrderController extends Controller
{
  
  use Common,SendMail;
      
  public function generateOrder(Request $request){
    
    try{
      $status = 0;
      $message = "";
      $user  = JWTAuth::user();
      $validator = Validator::make($request->all(), [
        'plan_id' => 'required'                          
      ]);

      $params = [];  
      if($validator->fails()){
        $error = json_decode(json_encode($validator->errors()));
        if(isset($error->plan_id[0])){
          $message = $error->plan_id[0];
        }
        return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
      }
      
      $planData = Plan::getPlanById($request->plan_id);
      //print_r($planData); die;
      if(isset($planData->id)){        
       $amount = (($request->price) ? $planData->plan_rate : $planData->plan_rate)*100;
       //$amount = 100;
       $receipt = 'order_'.uniqid();

       if(env('RAZOR_LIVE')){
          $key = env('RAZOR_LIVE_KEY');
          $secret = env('RAZOR_LIVE_SECRET');
        }else{
          $key = env('RAZOR_TEST_KEY','rzp_test_oX5Polbisfd3oT');
          $secret = env('RAZOR_TEST_SECRET','cWrzjpwvtSS5qzVtiiuHTYj0');
        }
         
        $api = new Api($key, $secret);

        $data  = $api->order->create(array('receipt' => $receipt, 'amount' => $amount, 'currency' => 'INR')); // Creates order
       
        
        if (empty($data)) {
          $data = FALSE;
        } else {
            $result = $data;              
            if(!isset($result->receipt)){
              return response()->json(['status'=>$status,'message'=>'Error','data'=>$result]);                                      
            }

            $order = new Order();
            $order->order_id = $result->receipt;
            $order->razor_id = $result->id; 
            $order->amount = ($result->amount/100);
            $order->user_id = $user->id;
            $order->plan_id = $request->plan_id;
            $order->currency = $result->currency;


            if($order->save()){
	      $diviceIds = $user->firebase_token;
              if($diviceIds){
                    $suscription_title = "Order Ctreation";
                    $suscription_msg = "Your Order Created SuccessFully";
                    //$this->sendNotification($diviceIds,'',$suscription_title,$suscription_msg);
                    //Mail::to($user->email)->send(new SubscriptionMail($planData));
              }
              return response()->json(['status'=>1,'message'=>'Order Created SuccessFully','data'=>$order]);                                                               
            }              
        }          
      }else{
        return response()->json(['status'=>$status,'message'=>'No plan exist','data'=>json_decode("{}")]);                      
      }
      

    }catch(Exception $e){
      return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
    }
            
  }

    /**
     * Edit event method
     * @return success or error
     * 
     * */
    public function verifyPayment(Request $request){
      
      try{
        $status = 0;
        $message = "";
              
        $user  = JWTAuth::user();
        
        if(!isset($user->id)){
          return response()->json(["status"=>$status,"message"=>'User does not exist',"data"=>json_decode("{}")]);
        } 

        if(env('RAZOR_LIVE')){
          $key = env('RAZOR_LIVE_KEY');
          $secret = env('RAZOR_LIVE_SECRET');
        }else{
          $key = env('RAZOR_TEST_KEY','rzp_test_oX5Polbisfd3oT');
          $secret = env('RAZOR_TEST_SECRET','cWrzjpwvtSS5qzVtiiuHTYj0');
        }
    
        $api = new Api($key, $secret);

        $validator = Validator::make($request->all(), [
          'razorpay_payment_id' => 'required',          
          'razorpay_order_id'=>'required',
          'razorpay_signature'=>'required',
          'order_id'=>'required',
        ]);        

        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          if(isset($error->razorpay_payment_id[0])){
            $message = $error->razorpay_payment_id[0];
          }else if(isset($error->razorpay_order_id[0])){
            $message = $error->razorpay_order_id[0];
          }else if(isset($error->razorpay_signature[0])){
            $message = $error->razorpay_signature[0];
          }
          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }

	//dd("hi", $request->all());
        $success = false;
        if (!empty($request->razorpay_payment_id)) {
        
        try
            {
                $attributes = array(
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                );

                $api->utility->verifyPaymentSignature($attributes);
                $success = true;
            }
            catch(SignatureVerificationError $e)
            {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }

        }
        
        if ($success === true)
        {
           // DB::beginTransaction();

            $orderData = Order::where('order_id',$request->order_id)->first();
		
            if(isset($orderData->id)){
              //dd("hi", $orderData);
              $subs_data = Subscription::getSubsByPlan($orderData->plan_id,$user->id);

              if(isset($subs_data->id)){ 
                //already exist subscription
                $diviceIds = [$user->device_token];                                
                $next_bill_date = date('Y-m-d H:i:s',strtotime("+".$subs_data->plan_days." days"));
                //Subscription::where('id', $subs_data->id)->update(['next_bill_date' => $next_bill_date]);
                if($this->confirmOrder($orderData->id,$subs_data->id)){
                  $html = "Payment success/ Signature Verified";
                  if($diviceIds){
                    $suscription_title = "Subscription Successfully";
                    $suscription_msg = "Your plan is successfully updated";
                    $this->sendNotification($diviceIds,'',$suscription_title,$suscription_msg);
                  }

		
                  /** Mail send notification area */
                  $data = [];  
                  $data['to_email'] = $user->email;         ;
                  $data['from'] = config('app.MAIL_FROM_ADDRESS');         ;
                  $data['subject'] = 'Subscription payment success';
                  $data['name'] = $user->name;
                  $data['name1'] = $user->name;
                  $data['email'] = $user->email;
                  $data['phone'] = $user->phone;
                  $data['amount'] = $orderData->amount;
                  $data['message1'] = 'Your plan is renew successfully, Thanks you!';
                  
                  $this->SendMail($data,'subscription');

                  /** Mail send notification area end */


                  /** Mail send notification area */
                  $data = [];  
                  $data['to_email'] = config('app.PAYMENT_MAIL');         ;
                  $data['from'] = config('app.MAIL_FROM_ADDRESS');         ;
                  $data['subject'] = 'New subscription payment success';
                  $data['name'] = 'Admin';
                  $data['amount'] = $orderData->amount;
                  $data['message1'] = 'This is to inform you that one user updated his subscription, Below are the details';
                  
                  $data['name1'] = $user->name;
                  $data['email'] = $user->email;
                  $data['phone'] = $user->phone;

                  $this->SendMail($data,'subscription');

                  /** Mail send notification area end */

                  $suscription_title = "Subscription Successfully";
                $suscription_msg = "Your plan is successfully updated";

                  $notification = [];
                  $notification['title'] = $suscription_title;
                  $notification['message'] = $suscription_msg;
                  $notification['user_id'] = $user->id;
                  $notification['type'] = 'Transaction';                
                  Notification::saveNotification($notification);


                  return response()->json(['status'=>1,'message'=>$html,'data'=>'success']);
                }                

              }else{ 
                $subs = new Subscription();
		$status = 'active';
                $subs->user_id = $user->id;
                $subs->start_date = date('Y-m-d');              
                $subs->plan_id = $orderData->plan_id;
		$subs->status = $status;
                $planData = Plan::getPlanById($orderData->plan_id);
                //$subs->next_bill_date = date('Y-m-d',strtotime('+'.$planData->days.' days'));

    
                $diviceIds = [$user->device_token];
                $suscription_title = "Subscription Successfully";
                $suscription_msg = "You have successfully subscribed to ".$planData->plan_name;
               
                $notification = [];
                $notification['title'] = $suscription_title;
                $notification['message'] = $suscription_msg;
                $notification['user_id'] = $user->id;
                $notification['type'] = 'Transaction';                
                Notification::saveNotification($notification);

                
                if($subs->save()){
                  $this->confirmOrder($orderData->id,$subs->id);
                  $schedule_array = array("123", "246");
                  $rand_keys = array_rand($schedule_array, 1);
                  //$this->scheduleClass($user_id,$orderData->plan_id,$schedule_array[$rand_keys]);                
                  $html = "Payment success/ Signature Verified";
                  if($diviceIds){
                    $this->sendNotification($diviceIds,'',$suscription_title,$suscription_msg);
                  }                

                  
                  /** Mail send notification area */
                  $data = [];  
                  $data['to_email'] = config('app.PAYMENT_MAIL');         ;
                  $data['from'] = config('app.MAIL_FROM_ADDRESS');         ;
                  $data['subject'] = 'New subscription payment success';
                  $data['name'] = 'Admin';
                  $data['name1'] = $user->name;
                  $data['amount'] = $orderData->amount;
                  $data['message1'] = 'A new user subscription is added, Below are the details';
                  
                  $data['name1'] = $user->name;
                  $data['email'] = $user->email;
                  $data['phone'] = $user->phone;

                  $this->SendMail($data,'subscription');

                  $data['to_email'] = $user->email;         ;  
                  $data['name'] = $user->name;
                  $data['name1'] = $user->name;
                  $this->SendMail($data,'subscription');

                  /** Mail send notification area end */


                  return response()->json(['status'=>1,'message'=>$html,'data'=>'success']); 
                }
              }
              

              
              //DB::commit();              
              
            }else{
              return response()->json(['status'=>0,'message'=>'Order does not exist','data'=>json_decode("{}")]);
            }
            
            
        }
        else{
            $html = "<p>Your payment failed</p><p>{$error}</p>";

            return response()->json(['status'=>0,'message'=>$html,'data'=>$html]);
        }
        
                 
      }catch(Exception $e){
       // DB::rollback();
        return response()->json(['status'=>$status,'message'=>'Error','data'=>json_decode("{}")]);                    
      }
              
    }


    public function getsubscription(Request $request){
        $status = 0;
        $message = "";              
        $user  = JWTAuth::user();
        
        $result = Subscription::getSubsByUser($user->id);

        return response()->json(['status'=>1,'message'=>'','data'=>$result]);                    
    }

}
