<?php

namespace App\Classes;
header('Content-type:application/json;charset=utf-8');

class UploadFile {

    public $response = ['status'=>0,'message'=>'','path'=>''];
    public function __construct(){

    }
    public function upload($path,$folders='pages'){
        try {        

            if (!isset($_FILES['file']['error']) || is_array($_FILES['file']['error'])) {

                $this->response['status'] = 'error';
                $this->response['message'] = 'Invalid parameters.';
                $this->response['path'] = '';
                return json_encode($this->response);
                //throw new RuntimeException('Invalid parameters.');
            }
        
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'No file sent.';
                    $this->response['path'] = '';
                    return json_encode($this->response);
                    break;
                    //throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    //throw new RuntimeException('Exceeded filesize limit.');
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'Exceeded filesize limit.';
                    $this->response['path'] = '';
                    return json_encode($this->response);
                    break;
                default:
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'unknown error';
                    $this->response['path'] = '';
                    return json_encode($this->response);                    
            }
            

            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

            if(!in_array(strtolower($ext),['jpg','png','jpeg','pdf','gif'])){
                $this->response['status'] = 'error';
                $this->response['message'] = 'Please upload only JPG,GIF and PNG images';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }

            $filename = uniqid().str_replace(" ","",$_FILES['file']['name']);
            //$filepath = sprintf($path.'/%s_%s', uniqid(), $_FILES['file']['name']);            
            $filepath = $path.'/'.$filename;
            if (!move_uploaded_file($_FILES['file']['tmp_name'],$filepath)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'file not uploaded';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }
        
            // All good, send the response

            $this->response['status'] = 'ok';
            $this->response['message'] = 'file uploaded successfully';
            //$this->response['path'] = url('/')."/uploads/".$folders.'/'.$filename;
            $this->response['path'] = url('/')."/uploads/".$folders.'/'.$filename;
            $this->response['img_path'] = $filepath;
            return json_encode($this->response);
            // echo json_encode([
            //     'status' => 'ok',
            //     'path' => $filepath
            // ]);
        
        } catch (RuntimeException $e) {
            // Something went wrong, send the err message as JSON
            http_response_code(400);
        
            $this->response['status'] = 'error';
            $this->response['message'] = $e->getMessage();
            $this->response['path'] = '';
            return json_encode($this->response);
        }
    }



    public function multiUpload($k,$path, $folders='user'){
        //print_r($_FILES); die;
        try {        
            $type = 'Image';
            if (!isset($_FILES['file']['error'][$k]) || is_array($_FILES['file']['error'][$k])) {

                $this->response['status'] = 'error';
                $this->response['message'] = 'Invalid parameters.';
                $this->response['path'] = '';
                return json_encode($this->response);
                //throw new RuntimeException('Invalid parameters.');
            }
        
            switch ($_FILES['file']['error'][$k]) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'No file sent.';
                    $this->response['path'] = '';
                    return json_encode($this->response);
                    break;
                    //throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    //throw new RuntimeException('Exceeded filesize limit.');
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'Exceeded filesize limit.';
                    $this->response['path'] = '';
                    return json_encode($this->response);
                    break;
                default:
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'unknown error';
                    $this->response['path'] = '';
                    return json_encode($this->response);                    
            }
            

            $ext = pathinfo($_FILES['file']['name'][$k], PATHINFO_EXTENSION);

            if(!in_array(strtolower($ext),['jpg','png','pdf','jpeg','gif'])){
                $this->response['status'] = 'error';
                $this->response['message'] = 'Please upload only JPG,GIF and PNG images';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }

            $filename = uniqid().str_replace(" ","",$_FILES['file']['name'][$k]);
            //$filepath = sprintf($path.'/%s_%s', uniqid(), $_FILES['file']['name']);            
            $filepath = $path.'/'.$filename;
            if (!move_uploaded_file($_FILES['file']['tmp_name'][$k],$filepath)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'file not uploaded';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }
        
            // All good, send the response
            if(!in_array($ext,['jpg','png','pdf','jpeg','gif'])){
                $type = 'Video';
            }
            $this->response['status'] = 'ok';
            $this->response['message'] = 'file uploaded successfully';
            $this->response['type'] = $type;
            $this->response['path'] = url('/')."/uploads/".$folders.'/'.$filename;
            $this->response['img_path'] = $filepath;
            return json_encode($this->response);
            // echo json_encode([
            //     'status' => 'ok',
            //     'path' => $filepath
            // ]);
        
        } catch (RuntimeException $e) {
            // Something went wrong, send the err message as JSON
            http_response_code(400);
        
            $this->response['status'] = 'error';
            $this->response['message'] = $e->getMessage();
            $this->response['path'] = '';
            return json_encode($this->response);
        }
    }



    public function uploadByName($path,$name,$folders='pages'){
        try {        
            
            if (!isset($_FILES[$name]['error']) || is_array($_FILES[$name]['error'])) {

                $this->response['status'] = 'error1';
                $this->response['message'] = 'Invalid parameters.';
                $this->response['path'] = '';
                return json_encode($this->response);
                //throw new RuntimeException('Invalid parameters.');
            }
        
            switch ($_FILES[$name]['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->response['status'] = 'error12111';
                    $this->response['message'] = 'No file sent.';
                    $this->response['path'] = '';
                    return json_encode($this->response);
                    break;
                    //throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    //throw new RuntimeException('Exceeded filesize limit.');
                    $this->response['status'] = 'error12';
                    $this->response['message'] = 'Exceeded filesize limit.';
                    $this->response['path'] = '';
                    return json_encode($this->response);
                    break;
                default:
                    $this->response['status'] = 'errorddd';
                    $this->response['message'] = 'unknown error';
                    $this->response['path'] = '';
                    return json_encode($this->response);                    
            }
            

            $ext = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);

            if(!in_array(strtolower($ext),['jpg','png','jpeg','pdf','gif'])){
                $this->response['status'] = 'errorfss';
                $this->response['message'] = 'Please upload only JPG,GIF and PNG images';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }

            $filename = uniqid().str_replace(" ","",$_FILES[$name]['name']);
            //$filepath = sprintf($path.'/%s_%s', uniqid(), $_FILES['file']['name']);            
            $filepath = $path.'/'.$filename;
            if (!move_uploaded_file($_FILES[$name]['tmp_name'],$filepath)) {
                $this->response['status'] = 'errorvvvvv';
                $this->response['message'] = 'file not uploaded';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }
        
            // All good, send the response

            $this->response['status'] = 'ok';
            $this->response['message'] = 'file uploaded successfully';
            //$this->response['path'] = url('/')."/uploads/".$folders.'/'.$filename;
            $this->response['path'] = url('/')."/uploads/".$folders.'/'.$filename;
            $this->response['img_path'] = $filepath;
            return json_encode($this->response);
            // echo json_encode([
            //     'status' => 'ok',
            //     'path' => $filepath
            // ]);
        
        } catch (RuntimeException $e) {
            // Something went wrong, send the err message as JSON
            http_response_code(400);
        
            $this->response['status'] = 'error----';
            $this->response['message'] = $e->getMessage();
            $this->response['path'] = '';
            return json_encode($this->response);
        }
    }

}
