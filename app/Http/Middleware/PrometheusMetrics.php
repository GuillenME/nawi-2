<?php

namespace App\Http\Middleware;

use Closure;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;

class PrometheusMetrics
{
    protected $registry;
    protected $counter;

    public function __construct()
{
    $this->registry = \App\PrometheusExporter::getRegistry();

    $this->counter = $this->registry->getOrRegisterCounter(
        'app',
        'http_requests_total',
        'Total HTTP Requests',
        ['method', 'endpoint']
    );
}

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $this->counter->inc([
            $request->method(),
            $request->path()
        ]);

        return $response;
    }

    public function getRegistry()
    {
        return $this->registry;
    }
}
