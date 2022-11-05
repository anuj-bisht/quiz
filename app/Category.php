<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  use DB;
  
class Category extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','parent_id','image','file_path','category_id','description'
    ];

    public static function getAllList(){
        return Self::all();
    }

    public static function getCatList(){
        $category = Self::pluck('name','id')->all();   
        return $category;
    }

    public static function getSelectCat(){
        $category = Self::select('name','id')->get();   
        return $category;
    }

    public static function getCatDDWithTestEnabled(){
        $category = Self::where('is_test_enabled','Y')->pluck('name','id')->sortBy('name');   
        return $category;
    }


    public static function getSelectedCategory($user_id){
        $result = DB::table('category_users')
        ->select('category_users.category_id')                                                        
        ->join('users', 'users.id', '=', 'category_users.user_id')      
        ->join('categories', 'categories.id', '=', 'category_users.category_id')                                                                         
        ->where('category_users.user_id',$user_id)
        ->groupBy('category_users.category_id')        
        ->get();
        $catArr = [];
        if($result->count()){
            foreach($result as $k=>$v){
                $catArr[] = $v->category_id;
            }
        }
        //$result = json_decode(json_encode($result), true);

        //echo '<pre>'; print_r($catArr); die;

        return $catArr;
    }

    public static function getUserCatList($user_id){
        
        $result = DB::table('category_users')        
        ->join('users', 'users.id', '=', 'category_users.user_id')      
        ->join('categories', 'categories.id', '=', 'category_users.category_id')                                                                         
        ->where('category_users.user_id',$user_id)
        ->groupBy('category_users.category_id')        
        ->pluck('categories.name','categories.id');
        
        return $result;
    }

    public static function getCategoryById($id){
        
        $result = DB::table('categories')        
        ->where('id',$id)
        ->first();
        
        return $result;
    }
    
}
