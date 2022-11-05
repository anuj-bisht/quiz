<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    use Common;
    private $ctrl_name = 'Exam';
    private $view_name = 'exams';
    private $common_params;

    function __construct()
    {
        //  $this->middleware('permission:district-list', ['only' => ['index','show']]);
        //  $this->middleware('permission:district-list|district-create|district-edit|district-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:district-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:district-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:district-delete', ['only' => ['destroy']]);

        $this->common_params = ['ctrl_name'=>$this->ctrl_name,'view_name'=>$this->view_name,'language'=>''];
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
            $obj = \App\Exam::getAllExam($params);        
            // dd($obj);

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }

            if($total){
                foreach($obj as $k=>$v){
                    $obj[$k]->row_id = $k;
                    $obj[$k]->subject_name = $v->subject->subject_name;
                    $obj[$k]->chapter_name = "";
                    // $obj[$k]->chapter_name = $v->chapter->chapter_name;
                    // $obj[$k]->class_name = "";   
                    $obj[$k]->class_name = $v->chapter->subject->classes->class_name;   
                    // echo '<pre>';print_r($obj[$k]); die;
                    // echo '<pre>';print_r($v->chapter->subject->classes->class_name);


                }
            }
            
            $response["recordsFiltered"] = $total;
            $response["recordsTotal"] = $total;
            //response["draw"] = draw;
            $response["success"] = 1;
            $response["data"] = $obj;
            // dd($response);
            
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
        
        try {
            $message = "";
            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'subject_id' => 'required',
                'chapter_id' => 'required',
                'max_marks' => 'required',            
                'title' => 'required',
                'description' => 'required',
                'no_of_question'=>'required',
                'exam_type'=>'required',
            ]);
       
            $check = \App\Exam::getWhere(['class_id'=>$request->class_id,'subject_id'=>$request->subject_id]);
            if($check)
            {
           
                return redirect('admin/'.$this->view_name)->with('error','Error. exam already created for this subject and class');
            }
            
        
            $obj = new \App\Exam();
            
            // dd(implode(',',$request->chapter_id));
            $obj->class_id = $request->class_id;
            $obj->subject_id = $request->subject_id;
            $obj->chapter_id = implode(',',$request->chapter_id);        
            $obj->status = $request->status;        
            $obj->title = $request->title;
            $obj->no_of_question = $request->no_of_question;
            $obj->exam_type = $request->exam_type;            
            $hour = 0;
            $minute = 0;

            if(isset($request->hour)){
                $hour = $request->hour*60*60;
            }
            if(isset($request->minute)){
                $minute = $request->minute*60;
            }
            $total_second = ($hour+$minute);            
             

            $obj->duration = gmdate('H:i:s', $total_second);
            $obj->description = $request->description;
            $obj->max_marks = $request->max_marks;
                        

            if($obj->save()){
                return redirect('admin/'.$this->view_name)
                              ->with('success',$this->ctrl_name.' created successfully.');
              }else{
                return redirect('admin/'.$this->view_name)->with('error','Error.');
            }


        } catch (\Exception $e) {
            return redirect('admin/'.$this->view_name)->with('error',$e->getMessage());
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
        $result = \App\Exam::findOrFail($id);
        $this->common_params['exam'] = $result;

        $classes = \App\Classes::getClassDD();
        $this->common_params['classes'] = $classes;

        $this->common_params['class_id'] = $result->class_id;

        $subjects = \App\Subject::getSubjectDDByClass($this->common_params);
        $this->common_params['subjects'] = $subjects;

        $this->common_params['subject_id'] = $result->subject_id;

        $chapters = \App\Chapter::getChapterDDBySubject($this->common_params);
        $this->common_params['chapters'] = $chapters;
        $selectedChapter = \App\Exam::getExamById($id)->chapter_id;
        $this->common_params['chapters_selected'] = explode(',',$selectedChapter);
        


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
        try {
            $message = "";
            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'subject_id' => 'required',
                'chapter_id' => 'required',
                'max_marks' => 'required',            
                'title' => 'required',
                'description' => 'required',
                'no_of_question'=>'required',
                'exam_type'=>'required',
            ]);
        
            
            
            $obj = \App\Exam::findOrFail($id);
            $obj->class_id = $request->class_id;
            $obj->subject_id = $request->subject_id;
            $obj->chapter_id = implode(',',$request->chapter_id);        
            $obj->status = $request->status;        
            $obj->title = $request->title;
            $obj->no_of_question = $request->no_of_question;
            $obj->exam_type = $request->exam_type;
            $hour = 0;
            $minute = 0;

            if(isset($request->hour)){
                $hour = $request->hour*60*60;
            }
            if(isset($request->minute)){
                $minute = $request->minute*60;
            }
            $total_second = ($hour+$minute);            
             

            $obj->duration = gmdate('H:i:s', $total_second); 
            $obj->description = $request->description;
            $obj->max_marks = $request->max_marks;
                        

            if($obj->save()){
                return redirect('admin/'.$this->view_name)
                              ->with('success',$this->ctrl_name.' updated successfully.');
              }else{
                return redirect('admin/'.$this->view_name)->with('error','Error.');
            }


        } catch (\Exception $e) {
            return redirect('admin/'.$this->view_name)->with('error',$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = \App\Exam::deleteExam($id);
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
        $res = \App\Exam::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true,"message"=>"Your file has been deleted."]);
    }
}
