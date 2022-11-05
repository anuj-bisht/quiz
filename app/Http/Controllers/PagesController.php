<?php

namespace App\Http\Controllers;


use App\Pages;
use App\PageImage;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Classes\UploadFile;
//use Carbon\Carbon;

class PagesController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      try{        
       
        $pages = Pages::getAllPages('title');
        
        return view('pages.index',['pages'=>$pages]);
      }catch(Exception $e){
        abort(500, $e->message());
      }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function pageIndexAjax(Request $request){
     
      $draw = (isset($request->data["draw"])) ? ($request->data["draw"]) : "1";
      $response = [
        "recordsTotal" => "",
        "recordsFiltered" => "",
        "data" => "",
        "success" => 0,
        "msg" => ""
      ];
      try {
          
          $start = ($request->start) ? $request->start : 0;
          $end = ($request->length) ? $request->length : 10;
          $search = ($request->search['value']) ? $request->search['value'] : '';          
          $cond[] = ['id','<>',''];
          $pages = Pages::where('id','<>', '');          
          
          if ($request->search['value'] != "") {            
            $pages = $pages->where('title','LIKE',"%".$search."%")
            ->orWhere('created_at','LIKE',"%".$search."%");
          } 

          $total = $pages->count();
          if($end==-1){
            $pages = $pages->get();
          }else{
            $pages = $pages->skip($start)->take($end)->get();
          }
          
          $response["recordsFiltered"] = $total;
          $response["recordsTotal"] = $total;          
          $response["success"] = 1;
          $response["data"] = $pages;
          
        } catch (Exception $e) {    

        }
      

      return response($response);
    }


    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create(Request $request)
   {
     $response = ['success'=>0,"message"=>"","data"=>[]];
     try{
       //Log::info('create new user profile for user:');

       $obj = new Pages();       
       $obj->title = $request->input('title');
       $obj->description = $request->input('description');                 
       if($obj->save()){
        $response['success'] = 1;
        echo json_encode($response);
       }else{
        $response['message'] = 'unable to add';
        echo json_encode($response);
       }       
     }catch(Exception $e){
       Log::info('page add exception:='.$e->message());
       $response['message'] = 'Opps! Somthing went wrong';
       echo json_encode($response);
       abort(500, $e->message());
     }
   }

   /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      try{
        $obj = new Pages();
        $objData = $obj->findOrFail($id);
        
        return view('Admin/pages.edit', ['pages' => $objData]);

      }catch(Exception $e){
        abort(500, $e->message());
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function uploadFiles(Request $request, $id)
    {
      try{        
        
        $obj = new pages();
        $objData = $obj->findOrFail($id);
        $upload_handler = new UploadFile();
        $path = public_path('uploads/pages'); 
        
        $data = $upload_handler->upload($path,'pages');
        $res = json_decode($data);
        if($res->status=='ok'){
          $floorImgObj = new PageImage();
          $floorImgObj->image = $res->path;
          $floorImgObj->img_path = $res->img_path;
          $floorImgObj->page_id = $id;
          $floorImgObj->save();
        }
        echo $data; die;
      }catch(Exception $e){
        abort(500, $e->message());
      }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function imagelist(Request $request)
    {
      try{        
        $id = $request->id;
        $obj = new Pages();
        $objData = $obj->findOrFail($id);
        if($objData->count()){
          $result = PageImage::where('page_id',$id)->get();
          echo json_encode($result); die;
        }
      }catch(Exception $e){
        abort(500, $e->message());
      }
    }


    public function setDefault(Request $request){
      try{        
        
        $id = $request->id;
        $imageObj = new PageImage();
        $imageObj = $imageObj->findOrFail($id);     
        
        if($imageObj->count()>0){
          
          PageImage::where('page_id', $imageObj->page_id)->update(array('default' => 'N'));

          $imageObj2 = new PageImage();
          $imageObj2 = $imageObj2->findOrFail($id);
          $imageObj2->default = 'Y';
          $imageObj2->save();
          echo json_encode(["success"=>1,"message"=>"deleted"]); die;
        }else{
          echo json_encode(["success"=>1,"message"=>"Not deleted"]); die;
        }        
        
      }catch(Exception $e){
        echo json_encode(["success"=>1,"message"=>"Not deleted"]); die;
      }
    }

    public function deleteImage(Request $request){
      try{        
        $id = $request->id;   
        $imageObj = new PageImage();
        $imageObj = $imageObj->findOrFail($id);     
        if($imageObj->count()>0){
          @unlink($imageObj->img_path);
          $imageObj->delete();          
          echo json_encode(["success"=>1,"message"=>"deleted"]); die;
        }else{
          echo json_encode(["success"=>1,"message"=>"Not deleted"]); die;
        }        
        
      }catch(Exception $e){
        echo json_encode(["success"=>1,"message"=>"Not deleted"]); die;
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pages $pages,$id)
    {
      try{
        $pageData = $pages->findOrFail($id);
        $pageData->title = $request->input('title');
        $pageData->description = $request->input('description');
        $pageData->status = $request->input('status');

        $pageData->save();
        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', '"'.$request->input('title').'" page has been updated successfully');
        return redirect('admin/pages');
      }catch(Exception $e){
        abort(500, $e->message());
      }

    }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\User  $user
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request)
   {
     try{
       
       $id = $request->input('id');
       $page = new Pages();
       $data = $page->findOrFail($id);
       //print_r($userData); die;
       if($data->count()>0){
         $data->delete();
         echo json_encode(["success"=>1,"message"=>"deleted"]); die;
       }else{
        echo json_encode(["success"=>0,"message"=>"not deleted"]); die;
       }
       
     }catch(Exception $e){
       abort(500, $e->message());
     }

     //return view('users.index', ['users' => $users->getAllUser()]);
   }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function settings(Request $request)
    {
      
      try{
        
        $settings = new Setting();
        $settingsData = $settings->findOrFail(1);
        if($request->isMethod('post')){
         $settingsData->id = 1;      
         $settingsData->admin_email = $request->admin_email;
         $settingsData->fb_link = $request->fb_link;
         $settingsData->twitter_link = $request->twitter_link;
         $settingsData->linkedin_link = $request->linkedin_link;

         $settingsData->corporate_email = $request->corporate_email;
         $settingsData->corporate_phone = $request->corporate_phone;
         $settingsData->corporate_address = $request->corporate_address;
         $settingsData->home_about = $request->home_about;

         $settingsData->unit_email = $request->unit_email;
         $settingsData->unit_phone = $request->unit_phone;
         $settingsData->unit_address = $request->unit_address;

         $settingsData->copyright = $request->copyright;
         
         $settingsData->save();
 
         $request->session()->flash('message.level', 'success');
         $request->session()->flash('message.content', 'Record saved successfully');
         return redirect('admin/pages/settings');
 
        }else{
         return view('Admin.pages.settings',['settings'=>$settingsData]); 
        }
               
      }catch(Exception $e){
        Log::info('settings add exception:='.$e->message());
        $response['message'] = 'Opps! Somthing went wrong';
        echo json_encode($response);
        abort(500, $e->message());
      }
    }

    
}
