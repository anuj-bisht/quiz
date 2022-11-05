<?php

namespace App\Classes;
header('Content-type:application/json;charset=utf-8');

class UploadVideo {

    public $response = ['status'=>0,'message'=>'','path'=>'','file_name'=>''];
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

            if(!in_array(strtolower($ext),['mov','mp4'])){
                $this->response['status'] = 'error';
                $this->response['message'] = 'Please upload only Mov,Mp4 Videos';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }

            $filename = uniqid().str_replace(" ","",$_FILES['file']['name']);
            //$filepath = sprintf($path.'/%s_%s', uniqid(), $_FILES['file']['name']);            
            $filepath = $path.'/'.$filename;
            if (!move_uploaded_file($_FILES['file']['tmp_name'],$filepath)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'video not uploaded';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }
        
            // All good, send the response

            $this->response['status'] = 'ok';
            $this->response['message'] = 'video uploaded successfully';
            //$this->response['path'] = url('/')."/uploads/".$folders.'/'.$filename;
            $this->response['path'] = url('/')."/uploads/".$folders.'/'.$filename;
            $this->response['img_path'] = $filepath;
            $this->response['file_name'] = $_FILES['file']['name'];
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
                    $this->response['message'] = 'No video sent.';
                    $this->response['path'] = '';
                    return json_encode($this->response);
                    break;
                    //throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    //throw new RuntimeException('Exceeded filesize limit.');
                    $this->response['status'] = 'error';
                    $this->response['message'] = 'Exceeded videosize limit.';
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

            if(!in_array(strtolower($ext),['mov','mp4'])){
                $this->response['status'] = 'error';
                $this->response['message'] = 'Please upload only Mov, Mp4 videos';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }

            $filename = uniqid().str_replace(" ","",$_FILES['file']['name'][$k]);
            //$filepath = sprintf($path.'/%s_%s', uniqid(), $_FILES['file']['name']);            
            $filepath = $path.'/'.$filename;
            if (!move_uploaded_file($_FILES['file']['tmp_name'][$k],$filepath)) {
                $this->response['status'] = 'error';
                $this->response['message'] = 'videos not uploaded';
                $this->response['path'] = '';
                return json_encode($this->response);
                die;
            }
        
            // All good, send the response
            if(!in_array($ext,['mov','mp4'])){
                $type = 'Video';
            }
            $this->response['status'] = 'ok';
            $this->response['message'] = 'video uploaded successfully';
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
}
