<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
  protected $table = 'states';
  protected $fillable = ['name'];
  protected $hidden = ['_token'];

  public function country(){
    return $this->belongsTo('App\Country');
  }

  public function city(){
    return $this->hasMany('App\City');
  }

  public function user(){
    return $this->hasMany('App\User');
  }

  public function getSelect($is_select=false){
      $select = State::orderBy('id','DESC');
      if($is_select){
          return $select;
      }
      return $select->get();
  }

  public static function getStateByCountry($country_id){
    return self::where('country_id',$country_id)->get();
  }

  public static function getStateByCountryDD($country_id){
    return self::where('country_id',$country_id)->pluck('name','id')->sortBy('name');
  }
}
