<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;
use DB;

class UploadController extends Controller
{
    use Common;
    private $ctrl_name = 'Upload';
    private $view_name = 'uploads';
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
    public function csvupload(Request $request)
    {   
        
        $status = 0;
        $message = "";
        $targetDir = public_path()."/uploads/csvs/"; 
        

        try {
            $fileName = basename($_FILES['file']['name']); 
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 

            $targetFileName = $targetDir.uniqid()."_uploaded_".$fileName.'.'.$fileType;

            //if(strtolower($fileType) != 'csv'){
            //    $message = "Please upload only csv file";
            //    return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);    
            //}

            if(move_uploaded_file($_FILES['file']['tmp_name'], $targetFileName)){ 
                
                $data = file($targetFileName);                            
                $chunks = array_chunk($data,50);                
                $flag = 0;
                $data_arr = [];
                $header = [];
                $skipped = 0;
                $insertted = 0;
                foreach($chunks as $key => $chunk){     
                    
                    foreach($chunk as $k=>$v){
                        if($k==0 && $key==0){
                            $header = explode(",",$v);
                            foreach($header as $k=>$v){
                                $header[$k] = trim($v);
                            }
                            continue;
                        }
                        $vArr = explode(",",$v);

                        if($request->module_name=="districts"){
                            $data_arr[$k]['district_name'] = $vArr[0]; 
                            $data_arr[$k]['district_code'] = $vArr[1];  
                        }else if($request->module_name=="blocks"){
                            $columns = [
                                "Block Code",
                                "Block Name",                                
                                "District Code",
                                "District Name"
                            ];
                            $diff = array_diff($columns,$header);
                            
                            if(count($diff) > 0){
                                return response()->json(["status"=>0,"message"=>"Invalid csv format","data"=>json_decode("{}")]);
                            }
                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $districts = \App\District::getAllDistrict($params);
                            $districtArr = \App\District::districtNameIdArray($districts);
                            $vArr[2] = trim($vArr[2]);
                            if(isset($districtArr[$vArr[2]])){
                                $data_arr[$k]['block_code'] = trim($vArr[0]); 
                                $data_arr[$k]['block_name'] = trim($vArr[1]);  
                                $data_arr[$k]['district_id'] = $districtArr[$vArr[2]];  
                                $insertted++;
                            }else{
                                $skipped++;
                            }
                            
                            

                        }else if($request->module_name=="schools"){
                            $columns = [
                                "School Code",
                                "School Name",                                
                                "Address",
                                "Pincode",
                                "Block Code",
                                "District Code"
                            ];
                            $diff = array_diff($columns,$header);
                            
                            if(count($diff) > 0){
                                return response()->json(["status"=>0,"message"=>"Invalid csv format","data"=>json_decode("{}")]);
                            }
                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $districts = \App\District::getAllDistrict($params);
                            $districtArr = \App\District::districtNameIdArray($districts);

                            $blocks = \App\Block::getAllBlock($params);
                            $blockArr = \App\Block::blockNameIdArray($blocks);

                            $vArr[0] = trim($vArr[0]);    
                            $vArr[1] = trim($vArr[1]);
                            $vArr[2] = trim($vArr[2]);
                            $vArr[3] = trim($vArr[3]);
                            $vArr[4] = trim($vArr[4]);
                            $vArr[5] = trim($vArr[5]);
                            
                            if(isset($districtArr[$vArr[5]]) && !empty($districtArr[$vArr[5]]) && isset($blockArr[$vArr[4]]) && !empty($blockArr[$vArr[4]])){
                                
                                $data_arr[$k]['school_code'] = $vArr[0];
                                $data_arr[$k]['school_name'] = $vArr[1];
                                $data_arr[$k]['school_address'] = $vArr[2];
                                $data_arr[$k]['school_pincode'] = $vArr[3];
                                $data_arr[$k]['block_id'] = $blockArr[$vArr[4]];
                                $data_arr[$k]['district_id'] = $districtArr[$vArr[5]];
                                
                                $insertted++;
                            }else{
                                $skipped++;
                            }


                        }else if($request->module_name=="classes"){
                            $data_arr[$k]['class_name'] = trim($vArr[0]);    
                            $insertted++;                         
                        }else if($request->module_name=="subjects"){
                            $columns = [                                
                                "Subject Name",                                
                                "Subject Banner",
                                "Subject Logo",
                                "Class Name"
                            ];

                            $diff = array_diff($columns,$header);
                            
                            if(count($diff) > 0){
                                return response()->json(["status"=>0,"message"=>"Invalid csv format","data"=>json_decode("{}")]);
                            }
                            
                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $classes = \App\Classes::getAllClasses($params);
                            $classArr = \App\Classes::classNameIdArray($classes);
                            $vArr[3] = trim($vArr[3]);
                            
                            if(isset($classArr[$vArr[3]]) && !empty($classArr[$vArr[3]])){
                                $data_arr[$k]['subject_name'] = trim($vArr[0]);  
                                
                                $vArr[1] = trim($vArr[1]);
                                if(!empty($vArr[1]) && getimagesize($vArr[1])){                                                                                
                                        $filename_from_url = parse_url($vArr[1]);
                                        $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);                                        $file_name = uniqid().".".$ext;
                                        
                                        copy($vArr[1], public_path()."/uploads/subjects/".$file_name);  
                                        $data_arr[$k]['subject_banner'] = url('/')."/uploads/subjects/".$file_name;  
                                        
                                        $data_arr[$k]['subject_banner_path'] = public_path()."/uploads/subjects/".$file_name;
                                }else{
                                    $data_arr[$k]['subject_banner'] = '';
                                    $data_arr[$k]['subject_banner_path'] = '';
                                }

                                $vArr[2] = trim($vArr[2]);
                                if(!empty($vArr[2]) && getimagesize($vArr[2])){                                                                                
                                        $filename_from_url = parse_url($vArr[2]);
                                        $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);                                        $file_name = uniqid().".".$ext;
                                        
                                        copy($vArr[2], public_path()."/uploads/subjects/logos/".$file_name);  
                                        $data_arr[$k]['subject_logo'] = url('/')."/uploads/subjects/logos/".$file_name;                                          
                                        $data_arr[$k]['subject_logo_path'] = public_path()."/uploads/subjects/logos".$file_name;
                                }else{
                                    $data_arr[$k]['subject_logo'] = '';
                                    $data_arr[$k]['subject_logo_path'] = '';
                                }

                                                                                                 
                                $data_arr[$k]['class_id'] = $classArr[$vArr[3]];    
                                $insertted++;            
                            }else{
                                $skipped++;
                                
                            }
                            
                            

                        }else if($request->module_name=="chapters"){

                            $columns = [                                
                                "Class Name",                                
                                "Subject Name",
                                "Chapter Name"
                            ];

                            $diff = array_diff($columns,$header);
                            
                            if(count($diff) > 0){
                                return response()->json(["status"=>0,"message"=>"Invalid csv format","data"=>json_decode("{}")]);
                            }

                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $classes = \App\Classes::getAllClasses($params);
                            $classArr = \App\Classes::classNameIdArray($classes);

                            $subjects = \App\Subject::getAllSubject($params);
                            $subjectArr = \App\Subject::subjectNameIdArray($subjects);
                            $vArr[0] = trim($vArr[0]);    
                            $vArr[1] = trim($vArr[1]);
                            if(isset($classArr[$vArr[0]]) && !empty($classArr[$vArr[0]]) && isset($subjectArr[$vArr[1]]) && !empty($subjectArr[$vArr[1]])){
                                $data_arr[$k]['class_id'] = $classArr[$vArr[0]];
                                $data_arr[$k]['subject_id'] = $subjectArr[$vArr[1]];
                                $data_arr[$k]['chapter_name'] = trim($vArr[2]);
                                $insertted++;
                            }else{
                                $skipped++;
                            }

                            
                        }else if($request->module_name=="questions"){

                            $columns = [
                                "Class Name",
                                "Subject Name",                                
                                "Chapter",
                                "Language",
                                "Question",
                                "Option 1",
                                "Option 2",
                                "Option 3",
                                "Option 4",
                                "Correct Response",
                                "Image"
                            ];
                            
                            $diff = array_diff($columns,$header);
                            
                            if(count($diff) > 0){
                                return response()->json(["status"=>0,"message"=>"Invalid csv format","data"=>json_decode("{}")]);
                            }
                            $params = $this->commonQueryArr();
                            
                            $params['get_result'] = true;
                            //$params['status'] = 'Y';
                            $classes = \App\Classes::getAllClasses($params);
                            $classArr = \App\Classes::classNameIdArray($classes);

                            $subjects = \App\Subject::getAllSubject($params);
                            $subjectArr = \App\Subject::subjectNameIdArray($subjects);

                            $chapters = \App\Chapter::getAllChapter($params);
                            $chaptertArr = \App\Chapter::chapterNameIdArray($chapters);

                            $languages = \App\Language::getAllLanguage($params);
                            $languageArr = \App\Language::languageNameIdArray($languages);

                            $class_id = 0;
                            $subject_id = 0;
                            $chapter_id = 0;
                            $language_id = 1;
                            $is_image = 'N';
                            
                            $vArr[0] = trim($vArr[0]);
                            if(isset($classArr[$vArr[0]])){
                                $class_id = $classArr[$vArr[0]];
                            }
                            
                            $vArr[1] = trim($vArr[1]);    
                            if(isset($subjectArr[$vArr[1]])){
                                $subject_id = $subjectArr[$vArr[1]];
                            }                                                        
                            
                            $vArr[2] = trim($vArr[2]);
                            if(isset($chaptertArr[$vArr[2]])){
                                $chapter_id = $chaptertArr[$vArr[2]];
                            }
                            
                            $vArr[3] = trim($vArr[3]);
                            if(isset($languageArr[$vArr[3]])){
                                $language_id = $languageArr[$vArr[3]];
                            }
                            
                            $question = trim($vArr[4]);
                            
                            $vArr[10] = trim($vArr[10]);
                            if(isset($vArr[10]) && $vArr[10]!=""){
                                $is_image = 'Y';
                            }
                            dd('hi',$chapter_id);
                            if($class_id==0 || $subject_id==0 || $chapter_id==0 || $vArr[4]=="" || $vArr[5]=="" || $vArr[6]==""){
                                $skipped++;
                                
                                echo "class_id=".$class_id." subject_id=".$subject_id." chapter_id".$chapter_id; die;
                                
                            }else{
                                
                                $qObj = new \App\Question();
                                $qObj->class_id = $class_id;
                                $qObj->subject_id = $subject_id;
                                $qObj->chapter_id = $chapter_id;
                                $qObj->language_id = $language_id;
                                $qObj->question = $question;
                                if($is_image=="Y"){
                                    $vArr[10] = trim($vArr[10]);
                                    if(!empty($vArr[10]) && getimagesize($vArr[10])){                                        
                                        $qObj->is_image = 'Y';
                                        $filename_from_url = parse_url($vArr[10]);
                                        $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
                                        $file_name = uniqid().".".$ext;
                                        
                                        copy($vArr[10], public_path()."/uploads/questions/".$file_name);  
                                        $qObj->image = url('/')."/uploads/questions/".$file_name;  
                                        $qObj->file_path = public_path()."/uploads/questions/".$file_name;
                                    }
                                    
                                }
                                //dd($qObj);
                                if($qObj->save()){
                                    //question_id,option_name, is_corrent N Y
                                    
                                    $optionData = [];
                                    
                                    if(isset($vArr[5]) && !empty(trim($vArr[5]))){
                                        if(trim($vArr[9])==1){
                                            $is_correct = 'Y';
                                        }else{
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id'=>$qObj->id,'option_name'=>$vArr[5],'is_correct'=>$is_correct];
                                    }

                                    if(isset($vArr[6]) && !empty(trim($vArr[6]))){
                                        if(trim($vArr[9])==2){
                                            $is_correct = 'Y';
                                        }else{
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id'=>$qObj->id,'option_name'=>$vArr[6],'is_correct'=>$is_correct];
                                    }
                                    if(isset($vArr[7]) && !empty(trim($vArr[7]))){
                                        if(trim($vArr[9])==3){
                                            $is_correct = 'Y';
                                        }else{
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id'=>$qObj->id,'option_name'=>$vArr[7],'is_correct'=>$is_correct];
                                    }
                                    if(isset($vArr[8]) && !empty(trim($vArr[8]))){
                                        if(trim($vArr[9])==4){
                                            $is_correct = 'Y';
                                        }else{
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id'=>$qObj->id,'option_name'=>$vArr[8],'is_correct'=>$is_correct];
                                    }
                                    //dd($optionData);    
                                    $insertOption = \App\Option::insert($optionData);
                                    $insertted++;
                                    
                                }

                            }
                        
                        }
                        
                    }
                    
                    if($request->module_name=="questions"){
                        
                    }else{
                        DB::table($request->module_name)->insertOrIgnore($data_arr); 
                    }
                    
                    return response()->json(["status"=>1,"message"=>"CSV uploaded successfully","data"=>['inserted'=>$insertted,'skipped'=>$skipped]]);
                    
                    //CsvUploadProcess::dispatch($chunk,$new_filename);                                        
                }                           
                
            } 
                            
            return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        } catch (\Exception $e) {
            
        }
    }

    
    
}
