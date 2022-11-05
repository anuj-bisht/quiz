<?php

namespace App;
use Validator;
use App\Country;
use App\State;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  protected $table = 'cities';
  protected $fillable = ['name'];
  protected $hidden = ['_token'];

  public function state(){
    return $this->belongsTo('App\State');
  }

  public function getCountryStateCityByName($request){
    $country = Country::where('name' , $request->country)->get();  
    
    $data = ["error"=>0,"message"=>"","success"=>0,"data"=>[]];
    
    if($country->count() > 0){              
        $state = State::where([['name' , $request->state],['country_id',$country[0]->id]])->get();                                          
        if($state->count() > 0){
          $city = City::where([['name' , $request->city],['state_id',$state[0]->id]])->get();                                 
            if($city->count() > 0){
                $data["success"] = 1;
                $data['data']['country_id'] = $country[0]->id;
                $data['data']['state_id'] = $state[0]->id;
                $data['data']['city_id'] = $city[0]->id;
            }else{
              $data["error"] = 1;
              $data["message"] = $request->city." city not found";       
            }
        }else{
          $data["error"] = 1;
          $data["message"] = $request->state." state not found";       
        }
    }else{
      $data["error"] = 1;
      $data["message"] = $request->country." country not found";       
    }  

    return $data;
  }


  public function getSelect($is_select=false){
      $select = City::orderBy('id','Asc');
      if($is_select){
          return $select;
      }
      return $select->get();
  }
}
