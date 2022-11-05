<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    use Common;
    private $ctrl_name = 'School';
    private $view_name = 'schools';
    private $common_params;

    function __construct()
    {
        //  $this->middleware('permission:district-list', ['only' => ['index','show']]);
        //  $this->middleware('permission:district-list|district-create|district-edit|district-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:district-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:district-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:district-delete', ['only' => ['destroy']]);

        $this->common_params = ['ctrl_name'=>$this->ctrl_name,'view_name'=>$this->view_name];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("$this->view_name".'.index',$this->common_params);
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
            $end = ($request->length) ? $request->length : 20;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            $params = $this->commonQueryArr();
            $obj = \App\School::getAllSchool($params);        
            

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }

            //echo '<pre>';print_r($obj[0]->block->district->district_name);
            //die;
            if($total){
                foreach($obj as $k=>$v){
                    
                    $obj[$k]->row_id = $k;
                    // $obj[$k]->block_name = $v->block->block_name;
                    $obj[$k]->district_name = $v->district_name;
                    

                }
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
        $districts = \App\District::getDistricDD();
        $this->common_params['districts'] = $districts;
        return view("$this->view_name".".create",$this->common_params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'district_id'=>'required',

            'school_name' => 'required|unique:'.$this->view_name.',school_name',                      
            'school_code' => 'required|unique:'.$this->view_name.',school_code',                      
        ]);
          
        $res = \App\School::create($request->all());
    
        if($res){
          return redirect('admin/'.$this->view_name)
                        ->with('success',$this->ctrl_name.' created successfully.');
        }else{
          return redirect('admin/'.$this->ctrl_name)
                        ->with('error','Error.');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = \App\School::findOrFail($id);
        $this->common_params['school'] = $result;

        $districts = \App\District::getDistricDD();
        $this->common_params['districts'] = $districts;

        $params = $this->commonQueryArr();
        $params['district_id'] = $result->district_id;
        $params['status'] = 'Y';
        $params['get_result'] = true;
        
        // $blocks = \App\Block::ajaxGetBlockByDistrictDD($params);
        
        
        // $this->common_params['blocks'] = $blocks;

        return view("$this->view_name".'.edit',$this->common_params);
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
        
        $school = \App\School::findOrFail($id);

        
        request()->validate([
            'district_id'=>'required',            
            'school_name' => 'required',                      
            'school_code' => 'required|unique:schools,school_code,'.$school->id, 
            
        ]);

                
        $school->school_name = $request->school_name;       
        $school->school_code = $request->school_code;
        $school->district_id = $request->district_id;
        $school->block_id = '';
        $school->school_pincode = $request->school_pincode;
        $school->school_address = $request->school_address;
        $school->status = $request->status;
        
        $school->update();
    
        return redirect('admin/'.$this->view_name)
                        ->with('success',$this->ctrl_name.' updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = \App\School::deleteSchool($id);
        if($res){
            return redirect()->route("$this->view_name".".index")
                        ->with('success',$this->ctrl_name.' deleted successfully');
        }else{
            return redirect()->route("$this->view_name".".index")
                        ->with('error',$this->ctrl_name.' delete error');
        }
    }
    /**
     * Remove bulk resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyBulk(Request $request)
    {
        $idsString = $request->ids;
	$ids = explode(",",$idsString);//dd($ids);
        $res = \App\School::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true,"message"=>"Your file has been deleted."]);
    }
}
