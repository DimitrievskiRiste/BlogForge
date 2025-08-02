<?php

namespace App\Http\Middleware;

use App\Installer\Installer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return !Installer::isLocked() ? $next($request) : response()->json(['hasErrors' => true, 'install_locked' => true, 'message' => 'Installation is locked!'],400);
    }
}
