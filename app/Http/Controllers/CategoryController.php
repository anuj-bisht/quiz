<?php
    
namespace App\Http\Controllers;
    
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\UploadFile;
use App\Slot;
use DB;

    
class CategoryController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:category-list', ['only' => ['index','show']]);
         $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index','show']]);
         $this->middleware('permission:category-create', ['only' => ['create','store']]);
         $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
              
        return view('categories.index',[]);

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
            $end = ($request->length) ? $request->length : 10;
            $search = ($request->search['value']) ? $request->search['value'] : '';
            //echo 'ddd';die;
            $cond[] = [];
            
            //echo '<pre>'; print_r($users); die; categoryFilter
            $obj = Category::whereRaw('1 = 1');
                    
            
            if ($request->parent != "") {            
                $obj = $obj->where('parent_id',$request->parent);
            }

            if ($request->search['value'] != "") {            
              $obj = $obj->where('name','LIKE',"%".$search."%");
            } 
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('name',$sort);
            }


            $total = $obj->count();
            if($end==-1){
              $obj = $obj->get();
            }else{
              $obj = $obj->skip($start)->take($end)->get();
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
              
                
        return view('categories.create',[]);
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
            'name' => 'required|unique:categories,name',                      
        ]);
          
        $obj = new Category();

        $obj->name = $request->name;
        $obj->description = $request->description;

        if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
          $upload_handler = new UploadFile();
          $path = public_path('uploads/categories'); 
          $data = $upload_handler->upload($path,'categories');
          $res = json_decode($data);
          if($res->status=='ok'){
            $obj->image = $res->path;
            $obj->file_path = $res->img_path;
          }else{
            $request->session()->flash('message.level', 'error');
            $request->session()->flash('message.content', $res->message);
            return redirect('admin/categories/create');
          }
        }
    
        if($obj->save()){
          return redirect('admin/categories')
                        ->with('success','Category created successfully.');
        }else{
          return redirect('admin/categories')
                        ->with('error','Error.');
        }
        
        
    }
    

     /**
     * Display the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('categories.show',compact('category'));
    }
    

        
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Category $category)
    {      
        //echo '<pre>';print_r($request->id); die;
        $category = Category::findOrFail($request->id);
        return view('categories.edit',compact('category'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $category = Category::findOrFail($request->id);

        request()->validate([
            'name' => 'required|unique:categories,name,'.$category->id
        ]);

                
        $category->name = $request->name;       
        $category->description = $request->description;
        
        if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
          $upload_handler = new UploadFile();
          $path = public_path('uploads/categories'); 
          $data = $upload_handler->upload($path,'categories');
          $res = json_decode($data);
          if($res->status=='ok'){
            $category->image = $res->path;
            $category->file_path = $res->img_path;
          }else{
            $request->session()->flash('message.level', 'error');
            $request->session()->flash('message.content', $res->message);
            return redirect('admin/categories');
          }
        }

    
        $category->update();
    
        return redirect('admin/categories')
                        ->with('success','Category updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CategoryExpence  $CategoryExpence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
    
        return redirect()->route('categories.index')
                        ->with('success','Category deleted successfully');
    }


    public function makelfl(Request $request)
    {
        DB::table('categories')->update(array('is_lfl' => '0'));
        $category = Category::findOrFail($request->id);                         
        $category->is_lfl = '1';                       
        if($category->update()){
          return response()->json(['status'=>1,'message'=>'Record updated successfully','data'=>[]]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'No record to update','data'=>[]]);                    
        }
        
    
    }

    public function getCategoryById(Request $request)
    {        

        $validator = Validator::make($request->all(), [
          'category_id' => 'required'
        ]);
        $params = [];  

        if($validator->fails()){
          $error = json_decode(json_encode($validator->errors()));
          
          if(isset($error->category_id[0])){
            $message = $error->category_id[0];
          }

          return response()->json(["status"=>$status,"message"=>$message,"data"=>json_decode("{}")]);
        }

        $category = Slot::getSlotById($request->category_id);                         
        
        if($category->count()){
          return response()->json(['status'=>1,'message'=>'Record updated successfully','data'=>$category]);                    
        }else{
          return response()->json(['status'=>0,'message'=>'No record to update','data'=>[]]);                    
        }
        
    
    }

    

    
}
