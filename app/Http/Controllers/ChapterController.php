<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;


class ChapterController extends Controller
{

    use Common;
    private $ctrl_name = 'Chapter';
    private $view_name = 'chapters';
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
            $obj = \App\Chapter::getAllChapter($params);        
            

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }

            //echo '<pre>';print_r($obj[0]->subject->subject_name);
            //die;
            if($total){
                foreach($obj as $k=>$v){
                    $obj[$k]->row_id = $k;
                    $obj[$k]->class_name = $v->classes->class_name;
                    $obj[$k]->subject_name = $v->subject->subject_name;
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
        $classes = \App\Classes::getClassDD();
        $this->common_params['classes'] = $classes;

        $subjects = \App\Subject::getSubjectDD();
        $this->common_params['subjects'] = $subjects;

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
            'class_id'=>'required',
            'subject_id'=>'required',
            'chapter_name' => 'required',
	    'chapter_name_hindi' => 'required',                                
        ]);
          
        $res = \App\Chapter::create($request->all());
    	
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
        $result = \App\Chapter::findOrFail($id);
        $this->common_params['chapter'] = $result;

        $classes = \App\Classes::getClassDD();
        $this->common_params['classes'] = $classes;

        $subjects = \App\Subject::getSubjectDD();
        $this->common_params['subjects'] = $subjects;

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
        $chapter = \App\Chapter::findOrFail($id);

        
        request()->validate([
            'subject_id'=>'required',
            'class_id'=>'required',            
            'chapter_name' => 'required',
        ]);

                
        $chapter->chapter_name = $request->chapter_name;
	$chapter->chapter_name_hindi = $request->chapter_name_hindi;       
        $chapter->class_id = $request->class_id;
        $chapter->subject_id = $request->subject_id;                
        $chapter->status = $request->status;
        
        $chapter->update();
    
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
        $res = \App\Chapter::deleteChapter($id);
        if($res){
            return redirect()->route("$this->view_name".".index")
                        ->with('success',$this->ctrl_name.' deleted successfully');
        }else{
            return redirect()->route("$this->view_name".".index")
                        ->with('error',$this->ctrl_name.' delete error');
        }
    }

    public function ajaxGetChapterBySubject(Request $request){
        $status = 0;
        
        $params = $this->commonQueryArr();
        $params['status'] = 'Y';
        $params['get_result'] = true;
        
        $params['subject_id'] = $request->subject_id;

        $res = \App\Chapter::getChapterBySubject($params);
        if($res->count()){
            return response()->json(['status'=>1,'message'=>'','data'=>$res]);                        
        }

        return response()->json(['status'=>$status,'message'=>'No data Found','data'=>json_decode("{}")]);                    
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
        $idsString = $request->ids;
	$ids = explode(",",$idsString);//dd($ids);
        $res = \App\Chapter::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true,"message"=>"Your file has been deleted."]);
    }
    
}
