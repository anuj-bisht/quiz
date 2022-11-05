<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{

    public $timestamps = false;
    
    public function questions()
    {
        return $this->belongsTo('App\Question');
    }

    // protected $hidden = array('is_correct');

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public static function getOptionByQuestion($question_id){        
        $result = Self::where('question_id',$question_id)->get();        
        return $result;
    }
}
