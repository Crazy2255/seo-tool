<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCleanJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start output buffering
        ob_start();
        
        $response = $next($request);
        
        // If this is a JSON response, clean any output that might have been sent
        if ($response->headers->get('Content-Type') === 'application/json' || 
            $request->expectsJson() || 
            $request->is('*/bulk-invite')) {
            
            // Clean any output buffer
            if (ob_get_level()) {
                ob_clean();
            }
            
            // Ensure proper JSON headers
            $response->headers->set('Content-Type', 'application/json');
        }
        
        return $response;
    }
}
