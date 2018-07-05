<?php

namespace App\Http\Middleware;

use Closure;

class access
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $access)
    {
        $continue = false;
        foreach($request->user()->access as $a) {
            if($a->title == $access) {
                $continue = true;
                break;
            }
        }
        if($continue)
        return $next($request);
        return redirect()->back()->with('error', 'Access Denied! You do not have permission to access ' .$access. '.');
    }
}
