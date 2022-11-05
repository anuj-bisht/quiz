<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    use Common;
    private $ctrl_name = 'Block';
    private $view_name = 'blocks';
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
            $obj = \App\Block::getAllBlock($params);        
            

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }

            if($total){
                foreach($obj as $k=>$v){
                    $obj[$k]->row_id = $k;
                    $obj[$k]->district_name = $v->district->district_name;
                }
            }
            // echo '<pre>';print_r($obj);
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
            'block_name' => 'required|unique:'.$this->view_name.',block_name',                      
            'block_code' => 'required|unique:'.$this->view_name.',block_code',                      
        ]);
          
        $res = \App\Block::create($request->all());
    
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
        $result = \App\Block::findOrFail($id);
        $this->common_params['block'] = $result;

        $districts = \App\District::getDistricDD();
        $this->common_params['districts'] = $districts;

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
        $block = \App\Block::findOrFail($id);

        
        request()->validate([
            'district_id'=>'required',
            'block_name' => 'required|unique:'.$this->view_name.',block_name,'.$block->id,                      
            'block_code' => 'required|unique:'.$this->view_name.',block_code',                      
        ]);

                
        $block->block_name = $request->block_name;       
        $block->block_code = $request->block_code;
        $block->district_id = $request->district_id;
        $block->status = $request->status;
        
        $block->update();
    
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
        $res = \App\Block::deleteBlock($id);
        if($res){
            return redirect()->route("$this->view_name".".index")
                        ->with('success',$this->ctrl_name.' deleted successfully');
        }else{
            return redirect()->route("$this->view_name".".index")
                        ->with('error',$this->ctrl_name.' delete error');
        }
    }

    public function ajaxGetBlockByDistrict(Request $request){
        try{            
            $status = 0;
            $message = "";
                
            $validator = Validator::make($request->all(), [
                'id' => 'required'                           
            ]);

            
            if($validator->fails()){
                $error = json_decode(json_encode($validator->errors()));
                if(isset($error->category_id[0])){
                    $message = $error->id[0];
                }
                return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
            }
            $district_id = $request->id;
            $params = $this->commonQueryArr();
            $params['district_id'] = $district_id;
            $params['status'] = 'Y';
            $params['get_result'] = true;
            
            $result = \App\Block::ajaxGetBlockByDistrict($params);

            return response()->json(['status'=>1,'message'=>$message,'data'=>$result]);    

        }catch(Exception $e){
            return redirect()->route("errors.index")
                        ->with('error',$e->getMessage());
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
        $res = \App\Block::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true,"message"=>"Your file has been deleted."]);
    }
    
}
