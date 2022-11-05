<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
  public static function getAllPages($sortBy="title"){
    return Self::all()->sortBy("title");
  }
  
  public function getCmsBySlug($slug){
    return $page = DB::table('pages')                   
                   ->where('slug', '=', $slug)
                   ->first();
  }


}
