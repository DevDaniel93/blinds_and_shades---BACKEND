<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // $allowedOrigins = explode(',', env('CORS_ALLOWED_ORIGINS', '*'));

        // Handle pre-flight requests
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json([], 200)
                ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        // Set CORS headers for all responses
        return $response
            ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Get the allowed origin if it is in the list of allowed origins.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function getAllowedOrigin(Request $request): string
    {
        $allowedOrigins = explode(',', env('CORS_ALLOWED_ORIGINS', ''));
        $origin = $request->headers->get('Origin');

        return in_array($origin, $allowedOrigins) ? $origin : '';
    }
}
