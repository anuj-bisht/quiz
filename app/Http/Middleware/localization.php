<?php

namespace App\Http\Middleware;

use Closure;

class localization
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
        $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'en';
     	// set laravel localization
	
        app()->setLocale($local);
    	// continue request
	//dd($request);
    	$response = $next($request);

        // set Content Languages header in the response
        $response->headers->set('X-localization', $local);

        // return the response
        return $response;
    }
}
