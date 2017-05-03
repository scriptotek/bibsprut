<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class GoogleOauth
{
    protected $client;

    /**
     * Create a new middleware instance.
     *
     * @param Application|\Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct()
    {
        $this->client = \Google::getClient();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
