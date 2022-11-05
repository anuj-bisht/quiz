<?php
  
namespace App;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    protected $table = 'models';

    public function brand(){
        return $this->belongsTo('App\Brand');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model','brand_id','make','build'
    ];

    public static function getAllModels(){
        
        return Models::where('id','<>','0')->orderBy("model")->get();
    }

    public static function getModelByBrandId($id){
        return self::where('brand_id',$id)->orderBy("model")->get();
    }

    public static function getModelListByBrandId($id){
        return self::where('brand_id',$id)->pluck('model','id')->sortBy('model');
    }
    public static function validateModel($id){
        return self::where('id',$id)->first();
    }    
}
