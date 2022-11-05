<?php
  
namespace App\Http\Middleware;
use App\User;
use Closure;
use Auth;
   
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = User::getUserRole(Auth::user()->id);
        if($roles->name == 'Admin'){
            return $next($request);
        }
   
         return redirect('home')->with('error',"You don't have admin access.");
    }
}