<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class MenuPage extends Model
{
    protected $table = 'menu_page';
    protected $fillable = ['menu_id','page_id'];
    //protected $hidden = ['_token'];    
}
