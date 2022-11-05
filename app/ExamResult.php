<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'question_id','option_id'
    ];

    public function classes()
    {
        return $this->belongsTo('App\Classes');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Chapter');
    }

    

        
}
