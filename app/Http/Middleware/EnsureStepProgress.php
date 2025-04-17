<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureStepProgress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user  = auth()->user();
        $route = $request->route()->getName();

        if ($user->current_step === 5 && !$request->is('complete')) {
            return redirect()->route('user.complete.success');
        }

        $currentStep = $user->current_step;
        $stepFromRoute = $this->extractStepFromRoute($request->path());

        if ($stepFromRoute > $currentStep) {
            return redirect('/step-' . $currentStep);
        }

        return $next($request);
    }

    private function extractStepFromRoute(string $path): int
    {
        if (preg_match('/step-(\d+)/', $path, $matches)) {
            return (int) $matches[1];
        }

        return 1;
    }
}
