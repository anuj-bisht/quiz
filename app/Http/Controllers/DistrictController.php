<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;

class DistrictController extends Controller
{
    use Common;
    private $ctrl_name = 'District';
    private $view_name = 'districts';
    private $common_params;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        return view('districts.index',$this->common_params);
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
            $obj = \App\District::getAllDistrict($params);        
            

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }
            if($total){
                foreach($obj as $k=>$v){
                    $obj[$k]->row_id = $k;
                    
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
            'district_name' => 'required|unique:'.$this->view_name.',district_name',                      
            'district_code' => 'required|unique:'.$this->view_name.',district_code',                      
        ]);
          
        $res = \App\District::create($request->all());
    
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
        $district = \App\District::findOrFail($id);
        $this->common_params['district'] = $district;
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
        $district = \App\District::findOrFail($id);

        
        request()->validate([
            'district_name' => 'required|unique:'.$this->view_name.',district_name,'.$district->id,                      
            'district_code' => 'required|unique:'.$this->view_name.',district_code',                      
        ]);

                
        $district->district_name = $request->district_name;       
        $district->district_code = $request->district_code;
        
        $district->update();
    
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
        $res = \App\District::deleteDistrict($id);
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
        // dd($request->ids);
        $ids = $request->ids;
        $res = \App\District::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true,"message"=>"Your file has been deleted."]);
    }
}
