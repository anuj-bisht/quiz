<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use App\Classes\UploadFile;

class SubjectController extends Controller
{
    use Common;
    private $ctrl_name = 'Subject';
    private $view_name = 'subjects';
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
        return view($this->view_name.'.index',$this->common_params);
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
            $obj = \App\Subject::getAllSubject($params);        
            

            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
            }
            
            if($obj->count()){
              foreach($obj as $k=>$v){
                $obj[$k]->row_id = $k;
                $obj[$k]->class_name = $v->classes->class_name;
              }
            }
            //dd($obj[0]->classes->class_name);

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
            'subject_name' => 'required|unique:'.$this->view_name.',subject_name',  
            'class_id'=>'required',
            'status'=>'required'                                                     
        ]);

        $obj = new \App\Subject();

        if(isset($_FILES['subject_banner']['name'])) {                      
          $upload_handler = new UploadFile();
          $path = public_path('uploads/subjects'); 
          $data = $upload_handler->uploadByName($path,'subject_banner','subjects');
          $res = json_decode($data);           
          
          if($res->status=='ok'){
            $obj->subject_banner = $res->path;
            $obj->subject_banner_path = $res->img_path;                                
          }                                                                   
        } 
        
        if(isset($_FILES['subject_logo']['name'])) {                      
          $upload_handler = new UploadFile();
          $path = public_path('uploads/subjects/logos'); 
          $data = $upload_handler->uploadByName($path,'subject_logo','subjects/logos');
          $res = json_decode($data);           
          
          if($res->status=='ok'){
            $obj->subject_logo = $res->path;
            $obj->subject_logo_path = $res->img_path;                                
          }                                                                   
        } 
        
        $obj->class_id = $request->class_id;
        $obj->subject_name = $request->subject_name;

        if($obj->save()){
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
      $subject = \App\Subject::findOrFail($id);

      $classes = \App\Classes::getClassDD();
      $this->common_params['classes'] = $classes;

      $this->common_params['subject'] = $subject;
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
      $subject = \App\Subject::findOrFail($id);

        
      request()->validate([
          'subject_name' => 'required|unique:'.$this->view_name.',subject_name,'.$subject->id,                      
          'class_id' => 'required',                      
      ]);

      if(isset($_FILES['subject_banner']['name'])) {                      
        $upload_handler = new UploadFile();
        $path = public_path('uploads/subjects'); 
        $data = $upload_handler->uploadByName($path,'subject_banner','subjects');
        $res = json_decode($data);           
        
        if($res->status=='ok'){
          if(file_exists($subject->subject_banner_path)){
            unlink($subject->subject_banner_path);
          }
          
          $subject->subject_banner = $res->path;
          $subject->subject_banner_path = $res->img_path;                                
        }                                                                   
      } 
      
      if(isset($_FILES['subject_logo']['name'])) {                      
        $upload_handler = new UploadFile();
        $path = public_path('uploads/subjects/logos'); 
        $data = $upload_handler->uploadByName($path,'subject_logo','subjects/logos');
        $res = json_decode($data);           
        
        if($res->status=='ok'){
          if(file_exists($subject->subject_logo_path)){
            unlink($subject->subject_logo_path);
          }          
          $subject->subject_logo = $res->path;
          $subject->subject_logo_path = $res->img_path;                                
        }                                                                   
      } 

              
      $subject->subject_name = $request->subject_name;       
      $subject->class_id = $request->class_id;
      $subject->status = $request->status ?? 'Y';
      
      $subject->update();
  
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
      $res = \App\Subject::deleteSubject($id);
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
        $res = \App\Subject::whereIn('id',$ids)->delete();
        return response()->json(['success'=>true,"message"=>"Your file has been deleted."]);
    }
}
