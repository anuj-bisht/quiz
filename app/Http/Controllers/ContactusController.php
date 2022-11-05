<?php
    
namespace App\Http\Controllers;
    
use App\Contactus;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\SendMail;
use Mail;
    
class ContactusController extends Controller
{ 
    use SendMail;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:diet-list', ['only' => ['index','show']]);
        //  $this->middleware('permission:diet-list|diet-edit', ['only' => ['index','show']]);         
        //  $this->middleware('permission:diet-edit', ['only' => ['edit','update']]);         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
              
        return view('contactus.index',[]);

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
            $obj = Contactus::select('contactus.*');
                    
            
            if ($request->search['value'] != "") {            
              $obj = $obj->where('name','LIKE',"%".$search."%");
              $obj = $obj->orWhere('email','LIKE',"%".$search."%");
              $obj = $obj->orWhere('phone','LIKE',"%".$search."%");              
            } 
            
            if(isset($request->order[0]['column']) && $request->order[0]['column']==0){
                $sort = $request->order[0]['dir'];
                $obj = $obj->orderBy('name',$sort);
            }
            if(isset($request->order[1]['column']) && $request->order[1]['column']==1){
              $sort = $request->order[1]['dir'];
              $obj = $obj->orderBy('email',$sort);
            }
            if(isset($request->order[2]['column']) && $request->order[2]['column']==2){
                $sort = $request->order[2]['dir'];
                $obj = $obj->orderBy('phone',$sort);
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
     * Display the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function show(Contactus $contactus)
    {
        return view('contactus.show',compact('contactus'));
    }       
    
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {      
        //echo '<pre>';print_r($request->id); die;
        $contactus = Contactus::findOrFail($id);
        return view('contactus.edit',compact('contactus'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\category_expence  $category_expence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $contactus = Contactus::findOrFail($id);

         
         request()->validate([
            'message' => 'required'
        ]);

        $contactus->replied = 'Y';  
        $contactus->update();

        $data = [];
        $data['to_email'] = $contactus->email;
        $data['from'] = config('app.MAIL_FROM_ADDRESS');         ;
        $data['subject'] = 'LiveFitLife Response';
        $data['name'] = $contactus->name;
        $data['message1'] = $request->message;        
        $this->SendMail($data,'contactresp');
    
        return redirect('admin/contactus')
                        ->with('success','Replied successfully');
    }
    
}
