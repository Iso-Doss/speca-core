<?php

namespace Speca\SpecaCore\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->headers->get('locale') && in_array(strtolower($request->headers->get('locale')), ['en', 'fr'])) {
            Session::put('locale', strtolower($request->headers->get('locale')));
            App::setLocale(Session::get('locale'));
        }

        return $next($request);
    }
}
