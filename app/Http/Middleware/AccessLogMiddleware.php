<?php

namespace App\Http\Middleware;

use Closure;

use App\AccessLog;

class AccessLogMiddleware
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
        $route = $request->getRequestUri();
        $ip = $request->ip();
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $access_log = new AccessLog;
        $access_log->access_route = $route;
        $access_log->ip_address = $ip;
        $access_log->date = $date;
        $access_log->time = $time;
        $access_log->save();
        return $next($request);
    }
}
