<?php

namespace App;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use DB;

class Contactus extends Model
{
    protected $table = 'contactus';


    protected $fillable = ['name','email','message','phone'];
    //protected $hidden = ['_token'];

             
}
