<?php

namespace App;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  protected $table = 'countries';
  protected $fillable = ['name'];
  protected $hidden = ['_token'];
 

  public function state(){
    return $this->hasMany('App\State');
  }

  public function user(){
    return $this->hasMany('App\User');
  }

  public function getSelect($is_select=false){
      $select = Country::orderBy('created_at','DESC');
      if($is_select){
          return $select;
      }
      return $select->get();
  }

  public static function getAllCountry(){
    return self::where('id','<>',0)->pluck('name','id')->sortBy('name');
  }
}
