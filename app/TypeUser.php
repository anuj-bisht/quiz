<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class TypeUser extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public static function getUserTypeList(){
      return self::where('id','<>',0)->pluck('name','id')->sortBy('name');
    }

    public static function getUserTypeListDD(){
      return self::all();
    }
    
}
