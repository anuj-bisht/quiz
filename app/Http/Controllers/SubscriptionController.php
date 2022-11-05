<?php
    
namespace App\Http\Controllers;
    
use App\Subscription;
use App\Category;
use Illuminate\Http\Request;
use App\Classes\UploadFile;

    
class SubscriptionController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:plan-list', ['only' => ['index','show']]);
        //  $this->middleware('permission:plan-list|plan-create|plan-edit|plan-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:plan-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:plan-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:plan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
              
        return view('subscriptions.index',[]);

    }
    
    public function ajaxData(Request $request){
    
        $draw = (isset($request->data["draw"])) ? ($request->data["draw"]) : "1";
        $response = [
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => "",
          "success" => 0,
          "msg" => ""
        ];
        try {
            
            $start = (isset($request->start)) ? $request->start : 0;
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];
            
            //echo '<pre>'; print_r($users); die; categoryFilter
            $obj = Subscription::select('subscriptions.*','users.name as username',
            'plans.plan_name','plans.plan_rate','plans.days')            
            ->join('users','users.id','=','subscriptions.user_id')
            ->join('plans','plans.id','=','subscriptions.plan_id');        
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('plans.name','LIKE',"%".$search."%");
            } 
            
            // if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
            //     $sort = $request->order[0]['dir'];
            //     $obj = $obj->orderBy('plans.name',$sort);
            // }


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


      
        
}
