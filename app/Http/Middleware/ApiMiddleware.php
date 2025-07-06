<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //return $next($request);
        $hosts = config("api.allowed_hosts");
        $ip = $request->getClientIp();
        putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1');
        if(!empty($hosts)) {
            foreach($hosts as $host){
                $hostIp = gethostbyname($host);
                if($hostIp == $ip){
                    return $next($request);
                }
            }
            return Response("Not allowed",403);
        }
        return Response("Not a valid domain", 403);
    }
}
