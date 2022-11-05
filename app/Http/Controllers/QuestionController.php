<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;
use App\Classes\UploadFile;
use DB;

class QuestionController extends Controller
{
    use Common;
    private $ctrl_name = 'Question';
    private $view_name = 'questions';
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
            $obj = \App\Question::getAllQuestion($params); 

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }
		
            if($total){
                foreach($obj as $k=>$v){
		    $optionData = DB::table('options')->where('question_id',$obj[$k]->id)->get();
		    $countRes =0;
		    foreach($optionData as $val){
		    	$countRes++;
			if($val->is_correct == 'Y'){
				$obj[$k]['is_correct'] = $countRes;
			}
		    }
                    $obj[$k]->row_id = $k;
                    $obj[$k]->class_name = $v->classes->class_name;
                    $obj[$k]->chapter_name = $v->chapter->chapter_name_hindi;
                    $obj[$k]->subject_name = $v->subject->subject_name;
                    $obj[$k]->language_name = $v->language->language_name;
		    $obj[$k]->language_name = $v->language->language_name;
		if($optionData[0]){
		    $obj[$k]['option_english_1'] = $optionData[0]->option_name;
		    $obj[$k]['option_hindi_1'] = $optionData[0]->option_hindi;
		}else{
		    $obj[$k]['option_english_1'] = "";
		    $obj[$k]['option_hindi_1'] = "";
		}
		if($optionData[1]){
		    $obj[$k]['option_english_2'] = $optionData[1]->option_name;
		    $obj[$k]['option_hindi_2'] = $optionData[1]->option_hindi;
		}else{
		    $obj[$k]['option_english_2'] = "";
		    $obj[$k]['option_hindi_2'] = "";
		}
		if($optionData[2]){
		    $obj[$k]['option_english_3'] = $optionData[2]->option_name;
		    $obj[$k]['option_hindi_3'] = $optionData[2]->option_hindi;
		}else{
		    $obj[$k]['option_english_3'] = "";
		    $obj[$k]['option_hindi_3'] = "";
		}
		if($optionData[3]){
		    $obj[$k]['option_english_4'] = $optionData[3]->option_name;
		    $obj[$k]['option_hindi_4'] = $optionData[3]->option_hindi;
		}else{
		    $obj[$k]['option_english_4'] = "";
		    $obj[$k]['option_hindi_4'] = "";
		}
		
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
        $languages = \App\Language::getAllList();
        $this->common_params['classes'] = $classes;
        $this->common_params['languages'] = $languages;
        //User::inRandomOrder()->limit(5)->get();
        
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
        $validator = Validator::make($request->all(), [
            'class_id' => 'required',
            'subject_id' => 'required',
            'chapter_id' => 'required',
            'question' => 'required',
            'option' => 'required',
            'answer' => 'required',
            'language_id' => 'required'
        ]);
    	//dd($request->all());
        if($validator->fails()){
            $error = json_decode(json_encode($validator->errors()));
            if(isset($error->class_id[0])){
                $message = $error->class_id[0];
            }else if(isset($error->subject_id[0])){
                $message = $error->subject_id[0];
            }else if(isset($error->chapter_id[0])){
                $message = $error->chapter_id[0];
            }else if(isset($error->question[0])){
                $message = $error->question[0];
            }else if(isset($error->option[0])){
                $message = $error->option[0];
            }else if(isset($error->answer[0])){
                $message = $error->answer[0];
            }else if(isset($error->language_id[0])){
                $message = $error->language_id[0];
            }

            return response()->json(["status"=>0,"message"=>$message,"data"=>json_decode("{}")]);
        }
        
        $obj = new \App\Question();
        $obj->class_id = $request->class_id;
        $obj->subject_id = $request->subject_id;
        $obj->chapter_id = $request->chapter_id;
        $obj->question = $request->question;
	$obj->question_hindi = $request->question_hindi;
        $obj->status = $request->status;
        $obj->language_id = $request->language_id;
        if($request->lang_convert == ''){ 
            $obj->lang_convert = "No";
        }
        else{
            $obj->lang_convert = $request->lang_convert;
        }
	
        if(isset($_FILES['file']['name'])) {                      
            $upload_handler = new UploadFile();
            $path = public_path('uploads/questions'); 
            $data = $upload_handler->uploadByName($path,'file','questions');
            $res = json_decode($data);           
            
            if($res->status=='ok'){
              $obj->image = $res->path;
              $obj->file_path = $res->img_path;                                
            }                                                                   
        } 

	//dd($request->all());
        if($obj->save()){
            $data = [];
            foreach($request->option as $k=>$v){
                if(isset($request->answer[$k]) && $request->answer[$k]=='on'){
                    $answer = 'Y';
                }else{
                    $answer = 'N';
                }
		
                $data[] = ["question_id"=>$obj->id,"option_name"=>$v,"option_hindi"=>$request->option_hindi[$k],"is_correct"=>$answer];
            }

            $insert = \App\Option::insert($data);
            if($insert){
                return response()->json(["status"=>1,"message"=>"Question inserted successfully","data"=>json_decode("{}")]);
            }else{
                return response()->json(["status"=>0,"message"=>"Error","data"=>json_decode("{}")]);
            }
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
        $question = \App\Question::findOrFail($id);
	$options = DB::table('options')->where('question_id',$question->id)->get();
        //echo '<pre>';print_r($question->option); die;
        $classes = \App\Classes::getClassDD();
        $languages = \App\Language::getAllList();
	
        $params = $this->commonQueryArr();
        $params['status'] = 'Y';
        $params['get_result'] = true;
        $params['class_id'] = $question->class_id;        
        $subjects = \App\Subject::getSubjectDDByClass($params);
        $params['subject_id'] = $question->subject_id;
        $chapters = \App\Chapter::getChapterDDBySubject($params);

        $this->common_params['classes'] = $classes;
        $this->common_params['languages'] = $languages;
        $this->common_params['question'] = $question;
        $this->common_params['subjects'] = $subjects;
        $this->common_params['chapters'] = $chapters;
	$this->common_params['options'] = $options;
        
        //User::inRandomOrder()->limit(5)->get();
        
        return view("$this->view_name".".edit",$this->common_params);
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
        
        $validator = Validator::make($request->all(), [
            'class_id' => 'required',
            'subject_id' => 'required',
            'chapter_id' => 'required',
            'question' => 'required',
            'option' => 'required',
            'answer' => 'required',
            'language_id' => 'required'
        ]);
    
        if($validator->fails()){
            $error = json_decode(json_encode($validator->errors()));
            if(isset($error->class_id[0])){
                $message = $error->class_id[0];
            }else if(isset($error->subject_id[0])){
                $message = $error->subject_id[0];
            }else if(isset($error->chapter_id[0])){
                $message = $error->chapter_id[0];
            }else if(isset($error->question[0])){
                $message = $error->question[0];
            }else if(isset($error->option[0])){
                $message = $error->option[0];
            }else if(isset($error->answer[0])){
                $message = $error->answer[0];
            }else if(isset($error->language_id[0])){
                $message = $error->language_id[0];
            }

            return response()->json(["status"=>0,"message"=>$message,"data"=>json_decode("{}")]);
        }
        
        $obj = \App\Question::findOrFail($id);
        $obj->class_id = $request->class_id;
        $obj->subject_id = $request->subject_id;
        $obj->chapter_id = $request->chapter_id;
        $obj->question = $request->question;
	$obj->question_hindi = $request->question_hindi;
        $obj->status = $request->status;
        $obj->language_id = $request->language_id;
        if($request->lang_convert == ''){ 
            $obj->lang_convert = "No";
        }
        else{
            $obj->lang_convert = $request->lang_convert;
        }
        
        
	
        if(isset($_FILES['file']['name'])) {                      
            $upload_handler = new UploadFile();
            $path = public_path('uploads/questions'); 
            $data = $upload_handler->uploadByName($path,'file','questions');
            $res = json_decode($data);           
            
            if($res->status=='ok'){
              $obj->image = $res->path;
              $obj->file_path = $res->img_path;                                
            }                                                                   
        } 

	
        if($obj->save()){
            $data = [];
            $update = 0;
            foreach($request->option as $k=>$v){

                
                if(isset($request->answer[$k]) && $request->answer[$k]=='on'){
                    $answer = 'Y';
                }else{
                    $answer = 'N';
                }

                if(isset($request->option_id[$k])){
                    $to_update = \App\Option::findOrFail($request->option_id[$k]);
                    $to_update->option_name = $v;
		    if($k==0){ $to_update->option_hindi = $request->option_hindi_1; }
		    if($k==1){ $to_update->option_hindi = $request->option_hindi_2; }
		    if($k==2){ $to_update->option_hindi = $request->option_hindi_3; }
		    if($k==3){ $to_update->option_hindi = $request->option_hindi_4; }
                    $to_update->is_correct = $answer;
                    $to_update->save();
                    $update++;
                }else{ 
		    if($k==0){ $to_update->option_hindi = $request->option_hindi_1; }
		    if($k==1){ $to_update->option_hindi = $request->option_hindi_2; }
		    if($k==2){ $to_update->option_hindi = $request->option_hindi_3; }
		    if($k==3){ $to_update->option_hindi = $request->option_hindi_4; }                   
                    $data[] = ["question_id"=>$obj->id,"option_name"=>$v,"option_hindi"=>$to_update,"is_correct"=>$answer];
                }
                                
            }

            $insert = \App\Option::insertOrIgnore($data);
            
            if($insert || $update){
                return response()->json(["status"=>1,"message"=>"Question inserted successfully","data"=>json_decode("{}")]);
            }else{
                return response()->json(["status"=>0,"message"=>"Error","data"=>json_decode("{}")]);
            }
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
        $res = \App\Question::deleteQuestion($id);
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
        $idsString = $request->ids;
	$ids = explode(",",$idsString);//dd($ids);
        $res = \App\Question::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true,"message"=>"Your file has been deleted."]);
    }
}
