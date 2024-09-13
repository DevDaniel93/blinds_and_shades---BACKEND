<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomAbilityCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ability
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $ability)
    {
        if (! $request->user()->tokenCan($ability)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. You do not have the required ability.'
            ], 401);
        }

        return $next($request);
    }
}
