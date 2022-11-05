<?php
  
namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use DB;
  
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
  
    public function type_user(){
        return $this->belongsTo('App\TypeUser');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gender','type_user_id','social_id','social_type',
        'email', 'password','phone',
        'age','gender','image','file_path',
        'goal_id','address','otp','otp_expiration_time',
        'image_front','file_path_front','image_back','file_path_back','image_side',
        'file_path_side','device_id'
    ];
  
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
  
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function assignRole($role)
    {
        return $this->roles()->sync(
            Role::whereName($role)->firstOrFail()
        );
    }
    
    public function userProfile()
    {
        return $this->hasMany('App\UserProfile');
    }

    public static function getUsersList(){
        return self::pluck('name','id')->sortBy('name');
    }

    public static function getUserByType(){
        return self::pluck('name','id')->sortBy('name');
    }
    
    public static function getUserById($user_id){
        return self::with('userProfile')->where('id',$user_id)->first();
    }

    public static function getAdminUser(){
        
        $result = DB::table('users')
                ->select('users.name','users.email','users.id')                                                        
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')      
                 ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')                                                         
                ->where('roles.name','Admin')
                ->orderBy('users.name')
                ->first();
    
        return $result;
          
    }

    
	
	public static function getUserRole($user_id){
		$result = DB::table('model_has_roles')
                ->select('roles.name')                                                        
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')                                                              
                ->where('model_has_roles.model_id',$user_id)
                ->first();
    
        return $result;
	}
	
	    

    public static function getReview(){
        $result = DB::table('reviews')
                ->select('reviews.*','users.name',
                'users.email',
                'u1.name as trainer_name')     
                ->join('users', 'users.id', '=', 'reviews.user_id') 
                ->join('users  as u1', 'users.id', '=', 'reviews.rating_to')                                                                
                ->orderBy('users.name','asc')
                ->get();
    
        return $result;
        
    }

    
}
