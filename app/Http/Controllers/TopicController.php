<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    use Common;
    private $ctrl_name = 'Topic';
    private $view_name = 'topics';
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
            $obj = \App\Topic::getAllTopic($params);        
            

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
                    
                    $obj[$k]->chapter_name = $v->chapter->chapter_name;
                    $obj[$k]->subject_name = $v->subject->subject_name;
                    $obj[$k]->class_name = $v->class->class_name;
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
        $chapters = \App\Chapter::getChapterDD();
        $classes = \App\Classes::getClassDD();
        $subjects = \App\Subject::getSubjectDD();

        $this->common_params['chapters'] = $chapters;
        $this->common_params['classes'] = $classes;
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
            'chapter_id'=>'required',
            'class_id'=>'required',
            'subject_id'=>'required',
            'topic_name' => 'required|unique:'.$this->view_name.',topic_name',                                  
        ]);
          
        $res = \App\Topic::create($request->all());
    
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
        $result = \App\Topic::findOrFail($id);
        $this->common_params['topic'] = $result;

        $subjects = \App\Subject::getSubjectDD();
        $this->common_params['subjects'] = $subjects;

        $classes = \App\Classes::getClassDD();
        $this->common_params['classes'] = $classes;

        $chapters = \App\Chapter::getChapterDD();
        $this->common_params['chapters'] = $chapters;


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
        $topic = \App\Topic::findOrFail($id);

        
        request()->validate([
            'class_id'=>'required',
            'subject_id'=>'required',            
            'chapter_id'=>'required',            
            'topic_name' => 'required',                      
            'status' => 'required', 
            
        ]);

                
        $topic->class_id = $request->class_id;       
        $topic->subject_id = $request->subject_id;
        $topic->chapter_id = $request->chapter_id;
        $topic->topic_name = $request->topic_name;        
        $topic->status = $request->status;
        
        $topic->update();
    
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
        $res = \App\Topic::deleteTopic($id);
        if($res){
            return redirect()->route("$this->view_name".".index")
                        ->with('success',$this->ctrl_name.' deleted successfully');
        }else{
            return redirect()->route("$this->view_name".".index")
                        ->with('error',$this->ctrl_name.' delete error');
        }
    }
}
