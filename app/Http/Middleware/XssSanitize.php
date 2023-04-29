<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XssSanitize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$input) {
            if (is_string($input)) {
                $input = trim($input);
                $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                $input = stripslashes($input);
                $input = htmlentities($input, ENT_QUOTES, 'UTF-8');
            } elseif (is_int($input)) {
                $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
                $input = (int)$input;
            } else {
                $input = $input;
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
