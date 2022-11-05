<?php
    
namespace App\Http\Controllers;
    
use App\Plan;
use App\Category;
use Illuminate\Http\Request;
use App\Classes\UploadFile;
use Illuminate\Support\Facades\URL;
    
class PlanController extends Controller
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
              
        return view('plans.index',[]);

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
            $obj = Plan::select('plans.*')->whereRaw('1 = 1');
            
                    
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('plans.plan_name','LIKE',"%".$search."%");
            } 
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('plans.plan_name',$sort);
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
              
        $category = Category::getCatList();                
        return view('plans.create',compact('category'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      	$base_url = URL::to('/');
        request()->validate([
            'plan_name' => 'required|unique:plans,plan_name',  
            'days' =>'required',
            'plan_rate'=>'required',
	    'image' => 'required'          
        ]);
	
       $image = $request->image;
       if ($image) {
           $file = $request->file('image');//dd($request->all(),$file);
           $extention = $file->getClientOriginalExtension();
           $filename = time() . rand(0, 999) . '.' . $extention;
           $file->move('uploads/plan/', $filename);
           $db = $base_url.'/uploads/plan/' . $filename;
        } else {
           $db = null;
        }      
		//print_r($request->all()); die;
        //Plan::create([$request->all()
	Plan::create([
              'plan_name' => $request->plan_name,
              'description' => $request->description,
              'plan_rate' => $request->plan_rate,
	      'days' => $request->days,
              'image' => $db,
              'status' => 'Y',
        ]);
        
        return view('plans.index',[]);
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        return view('plans.show',compact('plan'));
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {      
        $plan = \App\Plan::findOrFail($id);  
        return view('plans.edit',compact('plan'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $base_url = URL::to('/');
        $plan = Plan::findOrFail($id);
         request()->validate([
            'plan_name' => 'required|unique:plans,plan_name,'.$plan->id,
            'days' =>'required',            
            'plan_rate'=>'required'       
        ]);
       $image = $request->image;
       if ($image) {
           $file = $request->file('image');//dd($request->all(),$file);
           $extention = $file->getClientOriginalExtension();
           $filename = time() . rand(0, 999) . '.' . $extention;
           $file->move('uploads/plan/', $filename);
           $db = $base_url.'/uploads/plan/' . $filename;
        } else {
           $db = $request->image1;
        }      
	$path = Plan::where('id', $request->id)->first();
                if (isset($path->image)) {
                    unlink($path->image);
                }
        //print_r($request->all()); die;                
        $plan->plan_name = $request->plan_name;        
        $plan->description = $request->description;        
        $plan->days = $request->days;          
        $plan->plan_rate = $request->plan_rate;
	$plan->image = $db;    
    
        $plan->update();
    
        return redirect()->route('plans.index')
                        ->with('success','Plan updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryExpence  $CategoryExpence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
    
        return redirect()->route('plans.index')
                        ->with('success','Plan deleted successfully');
    }

    
}
