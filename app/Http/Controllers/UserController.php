<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Category;
use App\Setting;
use App\Schedule;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use App\Http\Controllers\Traits\SendMail;
use App\Http\Controllers\Traits\Common;
use Mail;
use App\Classes\UploadFile;
use Auth;
use App\Demorequest;
use App\Events\UserRegistered;
use App\Jobs\BulkEmail;
    
class UserController extends Controller
{    
    use SendMail;
    use Common;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:user-list', ['only' => ['index','show']]);
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function ajaxData(Request $request){
    //dd("hi");
        $draw = (isset($request->data["draw"])) ? ($request->data["draw"]) : "1";
        $response = [
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => "",
          "success" => 0,
          "msg" => ""
        ];
        try {
            
            $start = ($request->start) ? $request->start : 0;
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];
            
            //echo '<pre>'; print_r($users); die; categoryFilter
	    
            $obj = User::select('users.*','roles.name as rolename')           
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id');
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('users.name','LIKE',"%".$search."%");
            } 
	    
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('users.name',$sort);
            }
	    
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==2){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('users.email',$sort);
            }
	    
            if(isset($request->order[0]['column']) && $request->order[0]['column']==3){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('users.phone',$sort);
            }
	    
            if(isset($request->order[0]['column']) && $request->order[0]['column']==4){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('roles.name',$sort);
            }
   	    
            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }
            
            $response["recordsFiltered"] = $total;
            $response["recordsTotal"] = $total;
            //response["draw"] = draw;
            $response["success"] = 1;
            $response["data"] = $obj;
            
          } catch (Exception $e) {    
            
          }
        
   
        return response($response);
      }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();   
        
        $categories = Category::getCatList();
        
        return view('users.create',compact('roles','categories'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        

        try{
            //DB::beginTransaction();
            $this->validate($request, [
                'name' => 'required',            
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|numeric|digits:10',
                'password' => 'required|same:confirm-password',
                'roles' => 'required'
            ]);
        
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);


            if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
                $upload_handler = new UploadFile();
                $path = public_path('uploads/users'); 
                $data = $upload_handler->upload($path,'users');
                $res = json_decode($data);
                if($res->status=='ok'){
                  $input['image'] = $res->path;
                  $input['file_path'] = $res->img_path;
                }else{
                  $request->session()->flash('message.level', 'error');
                  $request->session()->flash('message.content', $res->message);
                  return redirect('admin/users/create');
                }
            }
                
            $user = User::create($input);
            $user->assignRole($request->input('roles'));

            //print_r($request->input('roles')); die;
            if(in_array("Trainer",$request->input('roles'))){
                $user->room_name = $request->name.'-'.$user->id;
                $user->save();
            }

            //add default rating

            $data = [];
            $data['to_email'] = $user->email;      
            $data['from'] = config('app.MAIL_FROM_ADDRESS');                  
            $data['subject'] = 'Account Created by Admin';                                            
            $data['message1'] = 'Your LiveFit account has been created by admin, Below are the details';
            $data['name'] = $request->name;              
            $data['password'] = $request->password;              
            $data['email'] = $request->email;
            $data['role'] = implode(",",$request->input('roles'));  
            $this->SendMail($data,'admin_create'); 

            DB::table('rating_users')->insert([
                'user_id'=>$user->value('id'),
                'description'=>'',
                'rating'=>5,
                'rating_by'=>Auth::user()->id
            ]);
                
            if(isset($request->categories) && count($request->categories)>0){
                $insert = [];
                foreach($request->categories as $k=>$v){
                    $insert[$k]['category_id'] = $v;
                    $insert[$k]['user_id'] = $user->value('id');
                }
                DB::table('category_users')->insert($insert);
            }            
            //DB::commit();
            return redirect()->route('users.index')
                            ->with('success','User created successfully');

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
        
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        //dd($user);
        $subscription = User::getClientFullDataById($id);
        //echo '<pre>';print_r($subscription); die;
        $schedule = Schedule::getAllSchedule($id,0);

        //echo '<pre>';print_r($schedule); die;
        return view('users.show',compact('user','subscription','schedule'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        
        $categories = Category::getAllList();
        $selectedCat = Category::getSelectedCategory($user->id);
        
        $userRole = $user->roles->pluck('name','name')->all();
        
    
        return view('users.edit',compact('user','roles','userRole','categories','selectedCat'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'nullable|numeric|digits:10',
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            
            $input = $request->except(['password']);
        }
    
        $user = User::find($id);

        if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
            $upload_handler = new UploadFile();
            $path = public_path('uploads/users'); 
            $data = $upload_handler->upload($path,'users');
            $res = json_decode($data);
            if($res->status=='ok'){
              $input['image'] = $res->path;
              $input['file_path'] = $res->img_path;
            }else{
              $request->session()->flash('message.level', 'error');
              $request->session()->flash('message.content', $res->message);
              return redirect('admin/users/create');
            }
        }

        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));

        $data = [];
        $data['to_email'] = $user->email;      
        $data['from'] = config('app.MAIL_FROM_ADDRESS');                  
        $data['subject'] = 'Account Created by Admin';                                            
        $data['message1'] = 'Your LiveFit account has been updated by admin, Below are the details';
        $data['name'] = $user->name;              
        $data['password'] = $request->password ?? 'Old one';              
        $data['email'] = $user->email;
        $data['role'] = implode(",",$request->input('roles'));  
        $this->SendMail($data,'admin_create'); 

        if(isset($request->categories) && count($request->categories)>0){
            DB::table('category_users')->where('user_id',$id)->delete();
            $insert = [];
            foreach($request->categories as $k=>$v){
                $insert[$k]['category_id'] = $v;
                $insert[$k]['user_id'] = $id;
            }
            DB::table('category_users')->insert($insert);
        } 
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        DB::table('user_profiles')->where('user_id',$id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }


    public function addRemoveNotification(Request $request){

        $status = 0;
        $message = "";

        try{
            $this->validate($request, [
                'ntype' => 'required',
                'id' => 'required',
                'val' => 'required'
            ]);
            $user = User::find($request->id);
            
            if($request->ntype=='email'){
                $value = ($request->val=='true') ? '1':'0';
                $user->email_notification = $value;
            }else{
                $value = ($request->val=='true') ? '1':'0';
                $user->sms_notification = $value;
            }
            
            $user->save();
            
            return response()->json(['status'=>1,'message'=>'','data'=>json_decode("{}")]);                    
        }catch(Exception $e){
			DB::rollback();
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 

        
    }

    public function getTrainerList(Request $request){
        
        $status = 0;
        $message = "";

        try{
            
            $user = User::getTrainerList();
                                    
            return response()->json(['status'=>1,'message'=>'','data'=>$user]);                    
        }catch(Exception $e){			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 
    }

    public function assigntrainer(Request $request){
        
        $status = 0;
        $message = "";

        try{
            $validator = Validator::make($request->all(), [
                'assign_to' => 'required',
                'trainers' =>'required'
            ]);                
            if($validator->fails()){
                $error = json_decode(json_encode($validator->errors()));
                if(isset($error->trainers[0])){
                    $message = $error->trainers[0];
                }else if(isset($error->assign_slot[0])){
                    $message = $error->assign_slot[0];
                }    
                    
                return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
            }
            
            if(isset($request->assign_to)){
                
                $ids = explode(",",$request->trainers); 
                $result = Demorequest::whereIn('id', $ids)->update(
                   [
                       'assign_to' => $request->assign_to,
                       'slot_time'=>$request->assign_slot,
                       'room_name'=>uniqid()
                   ]
                ); 
                if($result){
                    
                    $userlist = DB::table('demorequests')->select('demorequests.*','users.email','users.device_id')
                    ->whereIn('demorequests.id',$ids)
                    ->join('users','users.id','=','demorequests.user_id')
                    ->get();
                    //dd($userlist); die;
                    $diviceIds = [];
                    $emails = [];
                    if($userlist){
                        foreach($userlist as $key=>$value){
                            if(isset($value->device_id) && $value->device_id!=""){
                                $diviceIds[] = $value->device_id;        
                            }
                            $emails[] = $value->email;
                        }
                    }
                    
                    $data['to_email'] = config('app.from_email');
                    $data['bcc'] = $emails;
                    $data['from'] = config('app.MAIL_FROM_ADDRESS');         ;
                    $data['subject'] = 'Demo request assigned trainer';
                    $data['name'] = 'Sir';
                    $data['message1'] = 'Trainer is assined for your demo request';
                    $this->SendMail($data,'demorequest_assign_trainer');

                    //$diviceIds = ['c0peYFShRBOAE4IXMV0zM5:APA91bHZ-1H1UAlLLM5l1GZIallo1px30dS0SUPqSPIYb5UVZJuIVJ5jFoygRBrk0zULxqeBAHE_oeuDhrePHcM9_z_pH1UxW7aqyTPAIDeWpBAm3ABVFyuuqFKVvabzrnTDxguskIZR'];
                    //dd($diviceIds); die;
                    if(count($diviceIds)){
                        $this->sendNotification($diviceIds,'','Demo Request update','Your demo request is updated successfully');
                    }
                    echo "1"; die;
                    return response()->json(['status'=>1,'message'=>'','data'=>json_decode("{}")]);                    
                }else{
                    return response()->json(['status'=>$status,'message'=>'Unable to insert','data'=>json_decode("{}")]);                        
                }
                
            }  

            
        }catch(Exception $e){			
            return response()->json(['status'=>$status,'message'=>$message,'data'=>json_decode("{}")]);    
        } 
    }

    public function settings(Request $request, $id)
   {
     try{
       $id = $request->id;
       $settings = new Setting();
       $settingsData = $settings->findOrFail($id);
       if($request->isMethod('post')){

        //print_r($request->all()); die;
        $settingsData->id = 1;      
        $settingsData->admin_email = $request->admin_email;
        $settingsData->fb_link = $request->fb_link;
        $settingsData->twitter_link = $request->twitter_link;
        $settingsData->linkedin_link = $request->linkedin_link;
        $settingsData->insta_link = $request->insta_link;
        $settingsData->terms_and_condition = $request->terms_and_condition;
        $settingsData->privacy_policy = $request->privacy_policy;
        $settingsData->faq = $request->faq;

                
        $settingsData->save();

        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', 'Record saved successfully');
        return redirect('admin/users/settings/1');

       }else{
        return view('users.settings',['settings'=>$settingsData]); 
       }
              
     }catch(Exception $e){
       Log::info('settings add exception:='.$e->message());
       $response['message'] = 'Opps! Somthing went wrong';
       echo json_encode($response);
       abort(500, $e->message());
     }
   }
   
   function getAssignedClients(){
    $roles = User::getUserRole(Auth::user()->id);
		$clients = User::getAssignedClients(Auth::user()->id);
		return view('trainer.clients',compact('clients','roles'));
    }
    
    public function testemailsend(Request $request){
        $data = [];
        $data['to_email'] = 'ravindra2806@gmail.com';        
        $data['from'] = 'lfllivefitlife@gmail.com';
        $data['subject'] = 'Demo request assigned trainer';
        $data['name'] = 'Sir';
        $data['message1'] = 'Trainer is assined for your demo request';
       echo "==>".$this->SendMail($data,'testmail'); die;
    }

    public function sendNotifications(Request $request){
	$userData = User::orderBy('id','DESC')->get();
	return view('users.sendNotification',compact('userData'));
    }
    
    public function sendNotificationAllUser(Request $request){
        //dd("sendemailall");
	$user = User::get();
	foreach($user as $value){
            $diviceIds = [$value->device_token];
            if(isset($diviceIds)){
           	$suscription_title = "User Test";
                $suscription_msg = "User Test Successfully";
           	$this->sendNotification($diviceIds,'',$suscription_title,$suscription_msg);
            }
	}
	$userData = User::orderBy('id','DESC')->get();
	return view('users.sendNotification',compact('userData'));
    }

    public function sendNotificationUser(Request $request){
        $data = $request->name;//dd($request->all(),$request->name);
	$suscription_title = $request->title;
        $suscription_msg = $request->message;
	foreach($data as $value){
        	$user = User::where('id',$value)->first();
		$diviceIds = [$user->device_token];
        	if(isset($diviceIds)){
                    $data = $this->sendNotification($diviceIds,'',$suscription_title,$suscription_msg);
	            if($data){
			$userData = User::orderBy('id','DESC')->get();
			return view('users.sendNotification',compact('userData'));
	   	    }
        	}
	}
    }

    public function chkevent(){
        $user = User::where('id',9)->first();
        event(new UserRegistered($user));

        
        BulkEmail::dispatch($user);
        
        
    }

}
