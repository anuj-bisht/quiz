<?php 

namespace App\Http\Controllers\Traits; 
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Config;
use DB;
trait Common { 
    protected function DateDiff($data_from,$date_to) {     
        try{
            $t1 = strtotime(date('Y-m-d H:i:s'));
            $t2 = strtotime($date_to);
            $diff = $t2 - $t1;
            $hours = $diff / ( 60 * 60 );
            $hourFraction = $hours - (int)$hours;
            $minute = number_format($hourFraction*60,0);
            $data = floor($hours).':'.$minute;
            return $data;
          }catch(Exception $e){
            return 0;
          }  
    } 

    // protected function sendsms($phone,$message){
    
    //   $url = "https://api.msg91.com/api/sendhttp.php?mobiles=$phone&authkey=298624ASsBvRCXcI3o5f68c0deP1&route=4&sender=TPLGYM&message=$message&country=91";
    //   if(file_get_contents($url)){
    //     return true;
    //   }        
    // }


    protected function sendNotification($registration_ids,$icon,$messateTitle,$message,$data=[]) {
	
        //$values = array('name' => 'xyz');
	//DB::table('nit_data')->insert(['name' => 'wxyz']);
        $url = 'https://fcm.googleapis.com/fcm/send';
      
        //FCM requires registration_ids array to have correct indexes, starting from 0
        //$registration_ids = array_values($registration_ids);

        $fields = array(
          //'to' => json_encode($registration_ids),
          'registration_ids' => $registration_ids,
          'notification'=> array( "body" => $message,"title"=>$messateTitle,"icon"=>$icon),
          'data' => ['datakey'=>$data]
        );        			
        //print_r($registration_ids);die;
        $headers = array(
          'Authorization:key=AAAAnbcJUCI:APA91bEkQYOa9uGCky9m47qoTf89xglKQKzZFOPAnkoyX_ZKWptOXrMWHANdkeLmEYbzwtcZeYfi-6CWt0hQbut8ubiaWfmL4932kmH2Ziq_vDXtKzRgFvhcisCpMy0pGaIUKR235axx',//.Config('app.GOOGLE_API_KEY'),
          'Content-Type: application/json'
        ); 
	
        $ch= curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));    
        $res = curl_exec($ch);
        //dd($fields, $res, $headers);
        if($res === false)
          die('Curl failed ' . curl_error());
    
        curl_close($ch);
        return $res;
        
    }

    protected function commonQueryArr(){
      $params = ['status'=>false,'get_result'=>false];
      return $params;
    }
    
}

