<?php

namespace App\Http\Controllers;


use App\Page;
use App\PageImage;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Classes\UploadFile;
//use Carbon\Carbon;

class PageController extends Controller
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
        
        $pages = Page::getAllPages('title');
        
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

    public function ajaxData(Request $request){
     
      $draw = isset($request->data["draw"]) ? ($request->data["draw"]) : "1";
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
          $pages = Page::where('id','<>', '');          
          
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

      return view('pages.create',[]);

       //Log::info('create new user profile for user:');
       
     }catch(Exception $e){
       Log::info('page add exception:='.$e->message());
       $response['message'] = 'Opps! Somthing went wrong';
       echo json_encode($response);
       abort(500, $e->message());
     }
   }

   public function store(Request $request){

      request()->validate([
          'title'=>'required',
          'description'=>'required',
          'status' => 'required',                                  
      ]);

       $obj = new Page();       
       $obj->title = $request->input('title');
       $obj->description = $request->input('description');                 
       $obj->status = $request->input('status');   

       if($obj->save()){        
          return redirect('admin/pages')
                        ->with('success','Page created successfully.');
                  
        
       }else{
        return redirect('admin/pages')
                        ->with('error','Error.');
       }      
   }
   /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
      try{        
        $objData = Page::findOrFail($id);        
        return view('pages.edit', ['page' => $objData]);

      }catch(Exception $e){
        abort(500, $e->message());
      }
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      try{

        request()->validate([
          'title'=>'required',
          'description'=>'required',
          'status' => 'required',                                  
        ]);


        $pageData = Page::findOrFail($id);
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
   public function destroy($id)
   {
     try{
       
      
      $res = Page::where('id',$id)->delete();

      if($res){
          return redirect()->route("pages.index")
                      ->with('success','Page deleted successfully');
      }else{
          return redirect()->route("pages.index")
                      ->with('error','pages delete error');
      }

       
       
     }catch(Exception $e){
       abort(500, $e->message());
     }

     //return view('users.index', ['users' => $users->getAllUser()]);
   }

    

    
}
