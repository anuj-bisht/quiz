<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class StartExam extends Model
{
    protected $table = 'start_exam';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'exam_id','id','exam_date','user_id',
        'profile_id','start_timer','end_timer','result','status','percentage'
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function exam()
    {
        return $this->belongsTo('App\Exam');
    }
    
    public function user_profile()
    {
        return $this->belongsTo('App\UserProfile');
    }

    
    
}
