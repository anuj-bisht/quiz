<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\Validator;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\QuestionExport;

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

        $this->common_params = ['ctrl_name' => $this->ctrl_name, 'view_name' => $this->view_name];
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
        try {
            $empty_fields = array();
            $repeated_fields = array();
            if ($_FILES['file']['name'] != '') {
                $file_array = explode(".", $_FILES['file']['name']);

                $extension = end($file_array);

                if ($extension == 'csv') {
                    $file_data = fopen($_FILES['file']['tmp_name'], 'r');

                    $header = fgetcsv($file_data);


                    $limit = 0;
                    $data_arr = [];
                    $skipped = 0;
                    $insertted = 0;
                 

                    while (($vArr = fgetcsv($file_data)) !== FALSE) {
                        $limit++;
                        
                        // $new_empty_field = array();
                        // $new_repeated_field = array();
                        if ($request->module_name == "districts") {

                            for ($k = 0; $k < count($vArr); $k++) {

                                if (count($header) < 2) {
                                    return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
                                }
                                if ($k == 0) {
                                    $data_arr['district_name'] = $vArr[0];
                                }
                                if ($k == 1) {
                                    $data_arr['district_code'] = trim($vArr[1]);
                                }
                            }
                        } else if ($request->module_name == "schools") {

                            for ($k = 0; $k < count($vArr); $k++) {

                                if (count($header) < 3) {
                                    return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
                                }

                                $params = $this->commonQueryArr();
                                $params['get_result'] = true;

                                $districts = \App\District::getAllDistrict($params);
                                $districtArr = \App\District::districtNameIdArray($districts);
                                // $vArr[0] = str_replace(",","",$vArr[0]);  
                                // $vArr[1] = str_replace("/"," ",$vArr[1]);//$vArr[1];
                                // $vArr[2] = trim($vArr[2],',');

                                if ($districtArr[$vArr[2]] && !empty($districtArr[$vArr[2]])) {
                                    if ($k == 0) {
                                        $data_arr['school_code'] = $vArr[0];
                                    }
                                    if ($k == 1) {
                                        $data_arr['school_name'] = $vArr[1];
                                    }
                                    if ($k == 2) {
                                        $data_arr['district_id'] = $districtArr[$vArr[2]];
                                    }
                                    $insertted++;
                                } else {
                                    $skipped++;
                                }
                            }
                        } else if ($request->module_name == "classes") {

                            for ($k = 0; $k < count($vArr); $k++) {

                                if ($k == 0) {
                                    $data_arr['class_name'] = $vArr[0];
                                }
                                $insertted++;
                            }
                        } else if ($request->module_name == "subjects") {

                            for ($k = 0; $k < count($vArr); $k++) {

                                if (count($header) < 4) {
                                    return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
                                }

                                $params = $this->commonQueryArr();
                                $params['get_result'] = true;

                                $classes = \App\Classes::getAllClasses($params);
                                $classArr = \App\Classes::classNameIdArray($classes);

                                $vArr[3] = trim($vArr[3]);

                                if (isset($classArr[$vArr[3]]) && !empty($classArr[$vArr[3]])) {
                                    if ($k == 0) {
                                        $data_arr['subject_name'] = $vArr[0];
                                    }

                                    if ($k == 1) {
                                        $vArr[1] = $vArr[1];
                                        if (!empty($vArr[1]) && getimagesize($vArr[1])) {
                                            $filename_from_url = parse_url($vArr[1]);
                                            $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
                                            $file_name = uniqid() . "." . $ext;

                                            copy($vArr[1], public_path() . "/uploads/subjects/" . $file_name);
                                            $data_arr['subject_banner'] = url('/') . "/uploads/subjects/" . $file_name;

                                            $data_arr['subject_banner_path'] = public_path() . "/uploads/subjects/" . $file_name;
                                        } else {
                                            $data_arr['subject_banner'] = '';
                                            $data_arr['subject_banner_path'] = '';
                                        }
                                    }
                                    if ($k == 2) {
                                        $vArr[2] = $vArr[2];
                                        if (!empty($vArr[2]) && getimagesize($vArr[2])) {
                                            $filename_from_url = parse_url($vArr[2]);
                                            $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
                                            $file_name = uniqid() . "." . $ext;

                                            copy($vArr[2], public_path() . "/uploads/subjects/logos/" . $file_name);
                                            $data_arr['subject_logo'] = url('/') . "/uploads/subjects/logos/" . $file_name;
                                            $data_arr['subject_logo_path'] = public_path() . "/uploads/subjects/logos" . $file_name;
                                        } else {
                                            $data_arr['subject_logo'] = '';
                                            $data_arr['subject_logo_path'] = '';
                                        }
                                    }
                                    if ($k == 3) {
                                        $data_arr['class_id'] = $classArr[$vArr[3]];
                                    }
                                    $insertted++;
                                } else {
                                    $skipped++;
                                }
                            }
                        } else if ($request->module_name == "chapters") {

                            for ($k = 0; $k < count($vArr); $k++) {

                                if (count($header) < 4) {
                                    return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
                                }

                                $params = $this->commonQueryArr();
                                $params['get_result'] = true;

                                $classes = \App\Classes::getAllClasses($params);
                                $classArr = \App\Classes::classNameIdArray($classes);

                                $subjects = \App\Subject::getAllSubject($params);
                                $subjectArr = \App\Subject::subjectNameIdArray($subjects);
                                $vArr[$k] = trim($vArr[$k]);
                                $vArr[$k] = trim($vArr[$k]);

                                if (isset($classArr[$vArr[0]]) && !empty($classArr[$vArr[0]]) && isset($subjectArr[$vArr[1]]) && !empty($subjectArr[$vArr[1]])) {

                                    if ($k == 0) {
                                        $data_arr['class_id'] = $classArr[$vArr[0]];
                                    }
                                    if ($k == 1) {
                                        $data_arr['subject_id'] = $subjectArr[$vArr[1]];
                                    }
                                    if ($k == 2) {
                                        $data_arr['chapter_name'] = ""; //trim($vArr[2]);
                                    }
                                    if ($k == 3) {
                                        if ($vArr[1] == "English") {
                                            $data_arr['chapter_name_hindi'] = trim($vArr[2]);
                                        } else {
                                            $data_arr['chapter_name_hindi'] = trim($vArr[3]);
                                        }
                                        $insertted++;
                                    }
                                } else {
                                    $skipped++;
                                }
                            }
                        } else if ($request->module_name == "questions") {
                            if ($vArr[3] == "Hindi" && ($vArr[5] == "" || $vArr[10] == "" || $vArr[11] == "")) {
                                array_push($empty_fields, $vArr);
                                
                            }
                            for ($k = 0; $k < count($vArr); $k++) {
                                

                                if (count($header) < 16) {
                                    return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
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
                                //dd($vArr);
                                $vArr[0] = trim($vArr[0]);

                                if (isset($classArr[$vArr[0]])) {

                                    $class_id = $classArr[$vArr[0]];
                                }

                                $vArr[1] = trim($vArr[1]);
                                if (isset($subjectArr[$vArr[1]])) {
                                    $subject_id = $subjectArr[$vArr[1]];
                                }

                                $vArr[2] = trim($vArr[2]);
                                if (isset($chaptertArr[$vArr[2]])) {
                                    $chapter_id = $chaptertArr[$vArr[2]];
                                }

                                $vArr[3] = trim($vArr[3]);
                                if (isset($languageArr[$vArr[3]])) {
                                    $language_id = $languageArr[$vArr[3]];
                                }

                                $question = trim($vArr[4]);
                                if ($vArr[1] == "English") {
                                    $question_hindi = trim($vArr[4]);
                                } else {
                                    $question_hindi = trim($vArr[5]);
                                }
                                $q_id = DB::table('questions')->where('question_hindi', $question_hindi)->count();
                                $vArr[15] = trim($vArr[15]);

                                if (isset($vArr[15]) && $vArr[15] != "") {
                                    $is_image = 'Y';
                                }
                                // dd($is_image);
                                //dd('hi',$class_id,$subject_id,$chapter_id,$language_id,$question_hindi,$q_id);
                                if ($class_id == 0 || $subject_id == 0 || $chapter_id == 0 || $vArr[10] == "" || $vArr[11] == "" && $vArr[3] == "Hindi") {

                                    //dd('hi',$class_id,$subject_id,$chapter_id,$language_id,$question_hindi);
                                    $skipped++;
                                    continue;
                                } elseif ($q_id > 0) {
                                    array_push($repeated_fields, $vArr);
                                    $skipped++;
                                    continue;
                                } else {
                                    if ($k == 0) {
                                        $qObj = new \App\Question();
                                        $qObj->class_id = $class_id;
                                        $qObj->subject_id = $subject_id;
                                        $qObj->chapter_id = $chapter_id;
                                        $qObj->language_id = $language_id;
                                        $qObj->question = $question;
                                        $qObj->question_hindi = trim($question_hindi);
                                        // dd($qObj);
                                        if ($is_image == "Y") {
                                            $vArr[15] = trim($vArr[15]);
                                            if (!empty($vArr[15]) && getimagesize($vArr[15])) {
                                                $qObj->is_image = 'Y';
                                                $filename_from_url = parse_url($vArr[15]);
                                                $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
                                                $file_name = uniqid() . "." . $ext;
                                                // dd($file_name);
                                                copy($vArr[15], public_path() . "/uploads/questions/" . $file_name);
                                                $qObj->image = url('/') . "/uploads/questions/" . $file_name;
                                                $qObj->file_path = public_path() . "/uploads/questions/" . $file_name;
                                            }
                                        } else {
                                            $qObj->is_image = $is_image;
                                            $qObj->image = "Null";
                                            $qObj->file_path = "Null";
                                        }

                                        if ($qObj->save()) {
                                            $optionData = [];
                                            if (isset($vArr[10]) || isset($vArr[6])) {
                                                if (trim($vArr[14]) == 1) {
                                                    $is_correct = 'Y';
                                                } else {
                                                    $is_correct = 'N';
                                                }
                                                if ($vArr[1] == "English") {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[6], 'option_hindi' => trim($vArr[6]), 'is_correct' => $is_correct];
                                                } else {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[6], 'option_hindi' => trim($vArr[10]), 'is_correct' => $is_correct];
                                                }
                                            }

                                            if (isset($vArr[11]) || isset($vArr[7])) {
                                                if (trim($vArr[14]) == 2) {
                                                    $is_correct = 'Y';
                                                } else {
                                                    $is_correct = 'N';
                                                }
                                                if ($vArr[1] == "English") {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[7], 'option_hindi' => trim($vArr[7]), 'is_correct' => $is_correct];
                                                } else {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[7], 'option_hindi' => trim($vArr[11]), 'is_correct' => $is_correct];
                                                }
                                            }
                                            if (isset($vArr[12]) && isset($vArr[8])) {
                                                if (trim($vArr[14]) == 3) {
                                                    $is_correct = 'Y';
                                                } else {
                                                    $is_correct = 'N';
                                                }
                                                if ($vArr[1] == "English") {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[8], 'option_hindi' => trim($vArr[8]), 'is_correct' => $is_correct];
                                                } else {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[8], 'option_hindi' => trim($vArr[12]), 'is_correct' => $is_correct];
                                                }
                                            }
                                            if (isset($vArr[13]) && isset($vArr[9])) {
                                                if (trim($vArr[14]) == 4) {
                                                    $is_correct = 'Y';
                                                } else {
                                                    $is_correct = 'N';
                                                }
                                                if ($vArr[1] == "English") {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[9], 'option_hindi' => trim($vArr[9]), 'is_correct' => $is_correct];
                                                } else {
                                                    $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[9], 'option_hindi' => trim($vArr[13]), 'is_correct' => $is_correct];
                                                }
                                            }
                                            $insertted++;
                                            //dd($optionData);    
                                            $insertOption = \App\Option::insert($optionData);
                                        }
                                    }
                                }
                                // return json_encode($empty_fields);
                            }

                        }
                        $temp_data[] = $data_arr;
                    }
                    
                   

                    if ($request->module_name == "questions") {
                    } else {
                        DB::table($request->module_name)->insertOrIgnore($temp_data);
                    }

                    if (count($repeated_fields) > 0) {

                        // $this->testExcl($repeated_fields);
                        $request->session()->put('exceptionData', $empty_fields);
                        return response()->json(["status" => 2, "message" => "CSV uploaded successfully, Some fields are repeated", "data" => $empty_fields]);
                    }
                    if (count($empty_fields) > 0) {
                        $request->session()->put('exceptionData', $empty_fields);
                        return response()->json(["status" => 2, "message" => "CSV uploaded successfully, Some fields are missing", "data" => $empty_fields]);
                    }

                    return response()->json(["status" => 1, "message" => "CSV uploaded successfully", "data" => ['inserted' => $insertted, 'skipped' => $skipped]]);
                } else {
                    $error = 'Only <b>.csv</b> file allowed';
                }
            } else {
                $error = 'Please Select CSV File';
            }

            $output = array(
                'error'  => $error
            );

            // if(count($repeated_fields)>0){
            //     $this->testExcl($repeated_fields);
            // }
            // if(count($empty_fields)>0){
            //     $this->testExcl($empty_fields);
            // }
            // dd($empty_fields, $repeated_fields);
        } catch (\Exception $e) {
        }
    }
    public function csvupload1(Request $request)
    {

        $status = 0;
        $message = "";
        $targetDir = public_path() . "/uploads/csvs/";
        //dd($request->all());

        try {
            $fileName = basename($_FILES['file']['name']);
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $targetFileName = $targetDir . uniqid() . "_uploaded_" . $fileName . '.' . $fileType;

            if (strtolower($fileType) != 'csv') {
                $message = "Please upload only csv file";
                return response()->json(["status" => $status, "message" => $message, "data" => json_decode("{}")]);
            }

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFileName)) {

                $data = file($targetFileName);
                //dd($data);

                $chunks = array_chunk($data, 1000000);

                $flag = 0;
                $data_arr = [];
                $header = [];
                $skipped = 0;
                $insertted = 0;
                foreach ($chunks as $key => $chunk) {

                    foreach ($chunk as $k => $v) {
                        if ($k == 0 && $key == 0) {
                            $header = explode(",", $v);
                            foreach ($header as $k => $v) {
                                $header[$k] = trim($v);
                            }
                            continue;
                        }
                        //dd($v,$request->module_name);
                        if ($request->module_name == "questions") {
                            $var1 = [];
                            //$v = str_replace('"', '', $v);
                            $vArr = explode('"', $v);
                            $countVal = count($vArr);
                            for ($i = 0; $i < $countVal; $i++) {

                                if ($i > 0 && $i != ($countVal - 1)) {
                                    $vArr1[] = str_replace(',', '', $vArr[$i]) . ",";
                                } else {
                                    $vArr1[] = $vArr[$i];
                                }
                            }
                            $vArrImplode = implode('', $vArr1);
                            $vArrImplode = str_replace(',,', ',', $vArrImplode);
                            $vArr = explode(',', $vArrImplode);
                        } elseif ($request->module_name == "chapters") {

                            $vArr = explode(',', $v);

                            //$vArr[0] = substr_replace($vArr[0],'',-1,1);
                            //$vArr1 = $vArr[1];
                            //$vArr = explode(',',$vArr[0]);
                            //$vArr[count($vArr)] = $vArr1;

                            //dd("ch",$vArr);

                        } elseif ($request->module_name != "schools") {

                            $vArr = explode(",", $v);
                        } else {

                            $vArr = explode('"', $v);
                            if (count($vArr) < 3) {
                                $vArr = explode(',', $v);
                            }
                        }
                        //dd($vArr,$request->module_name);
                        if ($request->module_name == "districts") {
                            $data_arr[$k]['district_name'] = $vArr[0];
                            $data_arr[$k]['district_code'] = trim($vArr[1]);
                        } else if ($request->module_name == "blocks") {
                            $columns = [
                                "Block Code",
                                "Block Name",
                                "District Code",
                                "District Name"
                            ];
                            $diff = array_diff($columns, $header);

                            if (count($diff) > 0) {
                                return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
                            }
                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $districts = \App\District::getAllDistrict($params);
                            $districtArr = \App\District::districtNameIdArray($districts);
                            $vArr[2] = trim($vArr[2]);
                            if (isset($districtArr[$vArr[2]])) {
                                $data_arr[$k]['block_code'] = trim($vArr[0]);
                                $data_arr[$k]['block_name'] = trim($vArr[1]);
                                $data_arr[$k]['district_id'] = $districtArr[$vArr[2]];
                                $insertted++;
                            } else {
                                $skipped++;
                            }
                        } else if ($request->module_name == "schools") {

                            $columns = [
                                "School Code",
                                "School Name",
                                "District Code"
                            ];
                            $diff = array_diff($columns, $header);

                            if (count($diff) > 0) {
                                return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
                            }
                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $districts = \App\District::getAllDistrict($params);
                            $districtArr = \App\District::districtNameIdArray($districts);

                            //$blocks = \App\Block::getAllBlock($params);
                            //$blockArr = \App\Block::blockNameIdArray($blocks);
                            //dd("hi",trim($vArr[2],','),$districtArr[12]);
                            $vArr[0] = str_replace(",", "", $vArr[0]);
                            $vArr[1] = str_replace("/", " ", $vArr[1]); //$vArr[1];
                            $vArr[2] = trim($vArr[2], ',');
                            //$vArr[3] = trim($vArr[3]);
                            //$vArr[4] = trim($vArr[4]);
                            //$vArr[5] = trim($vArr[5]);
                            //dd($vArr[1],$vArr[2],$districtArr[(int)$vArr[2]]);
                            //if(isset($districtArr[$vArr[5]]) && !empty($districtArr[$vArr[5]]) && isset($blockArr[$vArr[4]]) ){
                            if ($districtArr[(int)$vArr[2]] && !empty($districtArr[(int)$vArr[2]])) {
                                //dd($vArr[0]);
                                $data_arr[$k]['school_code'] = $vArr[0];
                                $data_arr[$k]['school_name'] = $vArr[1];
                                //$data_arr[$k]['school_address'] = $vArr[2];
                                //$data_arr[$k]['school_pincode'] = $vArr[3];
                                //$data_arr[$k]['block_id'] = $blockArr[$vArr[4]];
                                $data_arr[$k]['district_id'] = $districtArr[(int)$vArr[2]];
                                //dd($data_arr[$k]);
                                $insertted++;
                            } else {
                                $skipped++;
                            }
                        } else if ($request->module_name == "classes") {
                            $data_arr[$k]['class_name'] = trim($vArr[0]);
                            $insertted++;
                        } else if ($request->module_name == "subjects") {
                            $columns = [
                                "Subject Name",
                                "Subject Banner",
                                "Subject Logo",
                                "Class Name"
                            ];

                            $diff = array_diff($columns, $header);

                            if (count($diff) > 0) {
                                return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
                            }

                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $classes = \App\Classes::getAllClasses($params);
                            $classArr = \App\Classes::classNameIdArray($classes);

                            $vArr[3] = trim($vArr[3]);

                            if (isset($classArr[$vArr[3]]) && !empty($classArr[$vArr[3]])) {
                                $data_arr[$k]['subject_name'] = trim($vArr[0]);

                                $vArr[1] = trim($vArr[1]);
                                if (!empty($vArr[1]) && getimagesize($vArr[1])) {
                                    $filename_from_url = parse_url($vArr[1]);
                                    $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
                                    $file_name = uniqid() . "." . $ext;

                                    copy($vArr[1], public_path() . "/uploads/subjects/" . $file_name);
                                    $data_arr[$k]['subject_banner'] = url('/') . "/uploads/subjects/" . $file_name;

                                    $data_arr[$k]['subject_banner_path'] = public_path() . "/uploads/subjects/" . $file_name;
                                } else {
                                    $data_arr[$k]['subject_banner'] = '';
                                    $data_arr[$k]['subject_banner_path'] = '';
                                }

                                $vArr[2] = trim($vArr[2]);
                                if (!empty($vArr[2]) && getimagesize($vArr[2])) {
                                    $filename_from_url = parse_url($vArr[2]);
                                    $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
                                    $file_name = uniqid() . "." . $ext;

                                    copy($vArr[2], public_path() . "/uploads/subjects/logos/" . $file_name);
                                    $data_arr[$k]['subject_logo'] = url('/') . "/uploads/subjects/logos/" . $file_name;
                                    $data_arr[$k]['subject_logo_path'] = public_path() . "/uploads/subjects/logos" . $file_name;
                                } else {
                                    $data_arr[$k]['subject_logo'] = '';
                                    $data_arr[$k]['subject_logo_path'] = '';
                                }


                                $data_arr[$k]['class_id'] = $classArr[$vArr[3]];
                                $insertted++;
                            } else {
                                $skipped++;
                            }
                        } else if ($request->module_name == "chapters") {

                            $columns = [
                                "Class Name",
                                "Subject Name",
                                "Chapter Name English",
                                "Chapter Name Hindi"
                            ];

                            //$diff = array_diff($columns,$header);
                            //dd($columns,$header,count($diff),$diff);
                            //if(count($diff) > 0){
                            //return response()->json(["status"=>0,"message"=>"Invalid csv format","data"=>json_decode("{}")]);
                            //}
                            $params = $this->commonQueryArr();
                            $params['get_result'] = true;

                            $classes = \App\Classes::getAllClasses($params);
                            $classArr = \App\Classes::classNameIdArray($classes);

                            $subjects = \App\Subject::getAllSubject($params);
                            $subjectArr = \App\Subject::subjectNameIdArray($subjects);
                            $vArr[0] = trim($vArr[0]);
                            $vArr[1] = trim($vArr[1]);
                            if (isset($classArr[$vArr[0]]) && !empty($classArr[$vArr[0]]) && isset($subjectArr[$vArr[1]]) && !empty($subjectArr[$vArr[1]])) {
                                $data_arr[$k]['class_id'] = $classArr[$vArr[0]];
                                $data_arr[$k]['subject_id'] = $subjectArr[$vArr[1]];
                                $data_arr[$k]['chapter_name'] = trim($vArr[2]);
                                $data_arr[$k]['chapter_name_hindi'] = trim($vArr[3]);
                                $insertted++;
                            } else {
                                $skipped++;
                            }

                            //dd("here",$data_arr);   
                        } else if ($request->module_name == "questions") {
                            $columns = [
                                "Class Name",
                                "Subject Name",
                                "Chapter",
                                "Language",
                                "Question",
                                "Question Hindi",
                                "Option 1",
                                "Option 2",
                                "Option 3",
                                "Option 4",
                                "Option Hindi 1",
                                "Option Hindi 2",
                                "Option Hindi 3",
                                "Option Hindi 4",
                                "Correct Response",
                                "Image"
                            ];

                            $diff = array_diff($columns, $header);

                            if (count($diff) > 0) {
                                return response()->json(["status" => 0, "message" => "Invalid csv format", "data" => json_decode("{}")]);
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
                            //dd($vArr);
                            $vArr[0] = trim($vArr[0]);

                            if (isset($classArr[$vArr[0]])) {

                                $class_id = $classArr[$vArr[0]];
                            }

                            $vArr[1] = trim($vArr[1]);
                            if (isset($subjectArr[$vArr[1]])) {
                                $subject_id = $subjectArr[$vArr[1]];
                            }

                            $vArr[2] = trim($vArr[2]);
                            if (isset($chaptertArr[$vArr[2]])) {
                                $chapter_id = $chaptertArr[$vArr[2]];
                            }

                            $vArr[3] = trim($vArr[3]);
                            if (isset($languageArr[$vArr[3]])) {
                                $language_id = $languageArr[$vArr[3]];
                            }

                            $question = trim($vArr[4]);

                            $question_hindi = trim($vArr[5]);

                            $vArr[15] = trim($vArr[15]);
                            //dd("ji",$vArr[15],$vArr);
                            if (isset($vArr[15]) && $vArr[15] != "") {
                                $is_image = 'Y';
                            }
                            //dd($class_id,$subject_id,$chapter_id,$vArr[2],$vArr[4],$vArr[5],$vArr);
                            if ($class_id == 0 || $subject_id == 0 || $chapter_id == 0 || $vArr[4] == "" || $vArr[5] == "" || $vArr[6] == "") {
                                $skipped++;

                                echo "class_id=" . $class_id . " subject_id=" . $subject_id . " chapter_id" . $chapter_id;
                                die;
                            } else {

                                $qObj = new \App\Question();
                                // dd($qObj);
                                $qObj->class_id = $class_id;
                                $qObj->subject_id = $subject_id;
                                $qObj->chapter_id = $chapter_id;
                                $qObj->language_id = $language_id;
                                $qObj->question = $question;
                                $qObj->question_hindi = $question_hindi;
                                //dd($vArr[15],$is_image,$vArr);
                                if ($is_image == "Y") {
                                    $vArr[15] = trim($vArr[15]);
                                    if (!empty($vArr[15]) && getimagesize($vArr[15])) {
                                        $qObj->is_image = 'Y';
                                        $filename_from_url = parse_url($vArr[15]);
                                        $ext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
                                        $file_name = uniqid() . "." . $ext;

                                        copy($vArr[15], public_path() . "/uploads/questions/" . $file_name);
                                        $qObj->image = url('/') . "/uploads/questions/" . $file_name;
                                        $qObj->file_path = public_path() . "/uploads/questions/" . $file_name;
                                    }
                                } else {
                                    $qObj->is_image = $is_image;
                                    $qObj->image = "Null";
                                    $qObj->file_path = "Null";
                                }
                                //dd($vArr[10],$vArr);
                                // dd($qObj);
                                // dd($qObj->save());

                                if ($qObj->save()) {
                                    //question_id,option_name, is_corrent N Y

                                    $optionData = [];
                                    if (isset($vArr[6]) && !empty($vArr[6])) {
                                        if (trim($vArr[14]) == 1) {
                                            $is_correct = 'Y';
                                        } else {
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[6], 'option_hindi' => $vArr[10], 'is_correct' => $is_correct];
                                    }

                                    if (isset($vArr[7]) && !empty($vArr[7])) {
                                        if (trim($vArr[14]) == 2) {
                                            $is_correct = 'Y';
                                        } else {
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[7], 'option_hindi' => $vArr[11], 'is_correct' => $is_correct];
                                    }
                                    if (isset($vArr[8]) && !empty($vArr[8])) {
                                        if (trim($vArr[14]) == 3) {
                                            $is_correct = 'Y';
                                        } else {
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[8], 'option_hindi' => $vArr[12], 'is_correct' => $is_correct];
                                    }
                                    if (isset($vArr[9]) && !empty($vArr[9])) {
                                        if (trim($vArr[14]) == 4) {
                                            $is_correct = 'Y';
                                        } else {
                                            $is_correct = 'N';
                                        }
                                        $optionData[] = ['question_id' => $qObj->id, 'option_name' => $vArr[9], 'option_hindi' => $vArr[13], 'is_correct' => $is_correct];
                                    }

                                    //dd($optionData);    
                                    $insertOption = \App\Option::insert($optionData);
                                    $insertted++;
                                }
                            }
                        }
                    }
                    //dd($data_arr);
                    if ($request->module_name == "questions") {
                    } else {
                        DB::table($request->module_name)->insertOrIgnore($data_arr);
                    }


                    return response()->json(["status" => 1, "message" => "CSV uploaded successfully", "data" => ['inserted' => $insertted, 'skipped' => $skipped]]);
                }
            }

            return response()->json(["status" => $status, "message" => $message, "data" => json_decode("{}")]);
        } catch (\Exception $e) {
        }
    }
    public function testExcl(Request $reques)
    {
        // dd($reques->session()->get('exceptionData'));
        
       return Excel::download(new QuestionExport($reques->session()->get('exceptionData')), 'exceptions.xlsx');
    }
}
