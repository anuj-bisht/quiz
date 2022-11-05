<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Menu;
use App\Pages;
use App\MenuPage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Classes\UploadFile;

class MenuController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        parent::__construct();
    }

     /*
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      //echo 'ddddd'; die;                 
       try{
         
         $obj = Menu::paginate(config('app.paging'));
         //echo '<pre>'; print_r($uesrs); die;
         return view('menus.index', ['data' => $obj]);
       }catch(Exception $e){
         abort(500, $e->message());
       }
   }
   

  /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function add(Request $request)
   {
     $response = ['success'=>0,"message"=>"","data"=>[]];
     try{
              
      //echo 'asdfasdfsad'; die;
       if($request->isMethod('post')){

        $validator = Validator::make($request->all(), [
          'name' => 'required|string',
          'slug' => 'required|string'
        ]);
        
        //$validator->errors()
        if($validator->fails()){
          $request->session()->flash('message.level', 'error');
          $request->session()->flash('message.content', $validator->errors());
          return redirect('admin/menus');
          
        }
        //print_r($_FILES); die;
        
        $obj = new Menu();       
        $obj->name = $request->input('name');
        $obj->slug = $request->input('slug');
        $obj->parent_id = 0;

        if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
          $upload_handler = new UploadFile();
          $path = public_path('uploads/menus'); 
          $data = $upload_handler->upload($path,'menus');
          $res = json_decode($data);
          if($res->status=='ok'){
            $obj->image = $res->path;
            $obj->file_path = $res->img_path;
          }else{
            $request->session()->flash('message.level', 'error');
            $request->session()->flash('message.content', $res->message);
            return redirect('admin/menus/add');
          }
        }
        
        if($obj->save()){
          $request->session()->flash('message.level', 'success');
          $request->session()->flash('message.content', '"'.$request->input('name').'" added successfully');
          return redirect('admin/menus');
        }else{
          $request->session()->flash('message.level', 'error');
          $request->session()->flash('message.content', '"'.$request->input('name').'" not added');
          return redirect('admin/menus/add');
        }
       }else{
        return view('menus/add'); 
       }
              
     }catch(Exception $e){
       Log::info('menus add exception:='.$e->message());
       $response['message'] = 'Opps! Somthing went wrong';
       echo json_encode($response);
       abort(500, $e->message());
     }
   }

   /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        return view('menus.show',compact('menu'));
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
        $obj = new Menu();
        $objData = $obj->findOrFail($id);
                
        return view('menus.edit', ['data' => $objData]);

      }catch(Exception $e){
        abort(500, $e->message());
      }
    }

        

    public function update(Request $request,$id)
    {
      try{
        $obj = new Menu();
        $obj = $obj->findOrFail($id);
        $obj->name = $request->input('name');
        $obj->slug = $request->input('slug'); 

        $validator = Validator::make($request->all(), [
          'name' => 'required|string',
          'slug' => 'required|string'
        ]);
        
        //$validator->errors()
        if($validator->fails()){
          $request->session()->flash('message.level', 'error');
          $request->session()->flash('message.content', $validator->errors());
          return redirect('admin/menus');
          
        }

        $objData = Menu::where([
            ['slug',$obj->slug],
            ['id','<>',$obj->id]
          ]
        )->get();      
        
        if($objData->count()){
          $request->session()->flash('message.level', 'error');
          $request->session()->flash('message.content', '"'.$request->input('slug').'" Already exist!');
          return redirect('admin/menus');  
        }

        if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
          $upload_handler = new UploadFile();
          $path = public_path('uploads/menus'); 
          $data = $upload_handler->upload($path,'menus');
          $res = json_decode($data);
          if($res->status=='ok'){
            $obj->image = $res->path;
            $obj->file_path = $res->img_path;
          }else{
            $request->session()->flash('message.level', 'error');
            $request->session()->flash('message.content', $res->message);
            return redirect('admin/menus/add');
          }
        }

        $obj->save();
        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', '"'.$request->input('name').'" clients updated successfully');
        return redirect('admin/menus');
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
    $response = ["status"=>0,"message"=>"Technical Error","data"=>[]];
     try{
       $obj = new Menu();
       $id = $request->input('id');
       $obj = $obj->findOrFail($id);

       $getChild = Menu::where('parent_id',$obj->id)->get();

       if($getChild->count()){
        $response['message'] = "Please delete all child before deleting parent"; 
        echo json_encode($response);
        die;
       }
      // print_r($obj); die;
       if($obj->count()>0){
         @unlink($obj->file_path);
         $obj->delete();
         $response['status'] = 1;
         $response['message'] = "Menu deleted successfully"; 
         echo json_encode($response);
         die;
       }else{
        $response['message'] = "Menu does not exist"; 
        echo json_encode($response);
        die;
       }
       
     }catch(Exception $e){
       abort(500, $e->message());
     }

     //return view('users.index', ['users' => $users->getAllUser()]);
   }


   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\User  $user
    * @return \Illuminate\Http\Response
    */
    public function assignpage(Request $request)
    {
     $response = ["status"=>0,"message"=>"Technical Error","data"=>[]];
      try{
        $obj = new Pages();        
        $id = $request->input('id');

        $selected_menu = MenuPage::where('menu_id',$id)->get();
        $selected = [];
        
        if($selected_menu->count()){
          foreach($selected_menu as $k=>$v){            
            $selected[] = $v->page_id;
          }
        }
        
        $getChild = Pages::all();
        
        if($getChild->count()){
         $response['status'] = 1; 
         $result = [];
         foreach($getChild as $k=>$v){
          $result[$k]['page_id'] = $v->id;
          //$result[$k]['menu_id'] = $v->menu_id;
          if(in_array($v->id,$selected)){
            $result[$k]['selected'] = true;
          }else{
            $result[$k]['selected'] = false;
          }
          $result[$k]['page_title'] = $v->title;
         }
         $response['data'] = $result;
         echo json_encode($response);
         die;
        }
        echo json_encode($response); die;
        
      }catch(Exception $e){
        abort(500, $e->message());
      }
 
      //return view('users.index', ['users' => $users->getAllUser()]);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\User  $user
    * @return \Illuminate\Http\Response
    */
    public function assignpageSubmit(Request $request)
    {     
      $response = ["status"=>0,"message"=>"Technical Error","data"=>[]];
      try{

      $data = [];
      $menu_id = $request->input('menu_id');
      $pages = $request->input('page_id');

      MenuPage::where('menu_id',$menu_id)->delete();
      foreach($pages as $k=>$v){        
        $data[$k]['menu_id'] = $menu_id;
        $data[$k]['page_id'] = $v;
      }        
      if(MenuPage::insert($data)){
        $response['status'] = 1;
        $response['message'] = "";
        
      }
      echo json_encode($response); die;
      }catch(Exception $e){
        abort(500, $e->message());
      }
 
    }
}
