<?php
    
namespace App\Http\Controllers;
    
use App\Notification;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Client;
use App\TypeUser;

use Spatie\Permission\Models\Role;
use DB;
use App\Http\Controllers\Traits\SendMail;
use App\Http\Controllers\Traits\Common;


    
class NotificationController extends Controller
{ 
    use SendMail,Common;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:notification-list|notification-create|notification-edit|notification-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:notification-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:notification-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:notification-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $userlist = User::getClientList();
        return view('notifications.index',compact('userlist'));

    }
    
    
    public function ajaxSendNotification(Request $request){
        //print_r($request->all()); die; 
        try{
            $response = ["status"=>0,"message"=>"","data"=>[]];
            
            $validator = Validator::make($request->all(), [
            'userlist' => 'required',          
            'subject' => 'required',    
            'message'=>'required'                          
            ]);
    
            $params = [];  
            if($validator->fails()){
                $error = json_decode(json_encode($validator->errors()));
                if(isset($error->userlist[0])){
                    $message = $error->userlist[0];
                }else if(isset($error->subject[0])){
                    $message = $error->subject[0];
                }else if(isset($error->message[0])){
                    $message = $error->message[0];
                }
                return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
            }
            $userArr = [];
            $diviceIds = [];
            foreach($request->userlist as $k=>$v){
                $data1[] = [              
                    'title' => $request->subject,
                    'message' => $request->message,
                    'user_id' => $v            
                ];  
                $userArr[] = $v;          
            }
            if(Notification::insert($data1)){
                $userlistObj = User::whereIn('id', $userArr)->get();
                
                if($userlistObj->count()){
                    foreach($userlistObj as $k=>$v){
                        if(isset($v->device_id) && !empty($v->device_id)){
                            $diviceIds[] = $v->device_id;
                        }                        
                    }
                    
                    if(count($diviceIds)){                        
                        $this->sendNotification($diviceIds,'','LFL Admin Notification','Admin has sent notification to you, Please go to mobile app and check it in detail.');
                    }
                }

                return response()->json(["status"=>1,"message"=>"Notification sent","data"=>[]]);        
            }else{
                return response()->json(["status"=>0,"message"=>"Something went wrong","data"=>[]]);        
            } 
            
        }catch(Exception $e){
            DB::rollBack();
            abort(500, $e->message());
        } 
        
    }

    
    
    
}
