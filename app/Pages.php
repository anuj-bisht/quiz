<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
  public static function getAllPages($sortBy="title"){
    return Pages::all()->sortBy("title");
  }

  public function page_images()
  {
        return $this->hasMany('App\PageImage','page_id','id');
  }

  public function getCmsBySlug($slug){
    return $page = DB::table('pages')
                   //->select('id','phone')
                   ->where('slug', '=', $slug)
                   ->get();
  }


}
