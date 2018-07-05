<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class canDelete
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
        if($request->user()->can_delete)
        return $next($request);
        return redirect()->back()->with('error', 'Denied! You do not have permission to Delete.');
    }
}
