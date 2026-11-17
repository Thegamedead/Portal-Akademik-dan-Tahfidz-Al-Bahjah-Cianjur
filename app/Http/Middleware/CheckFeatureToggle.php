<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FeatureToggle;

class CheckFeatureToggle
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $featureName): Response
    {
        if (!FeatureToggle::isEnabled($featureName)) {
            // If it's a livewire request, abort
            if ($request->hasHeader('X-Livewire')) {
                abort(403, "Fitur ini sedang dinonaktifkan.");
            }
            
            return redirect()->back()->with('toast', [
                'variant' => 'error',
                'message' => 'Fitur ' . str_replace('_', ' ', $featureName) . ' sedang dinonaktifkan.'
            ]);
        }

        return $next($request);
    }
}
