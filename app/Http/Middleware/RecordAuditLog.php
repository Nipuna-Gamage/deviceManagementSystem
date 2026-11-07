<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordAuditLog
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && $response->getStatusCode() < 400
            && ! $request->routeIs('login')) {
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => match ($request->method()) {
                    'POST' => 'create', 'PUT', 'PATCH' => 'update', 'DELETE' => 'delete', default => 'other',
                },
                'method' => $request->method(),
                'route' => $request->path(),
                'description' => 'Successful '.$request->method().' request to '.$request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'response_status' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }
}
