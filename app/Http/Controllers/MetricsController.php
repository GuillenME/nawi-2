<?php

namespace App\Http\Controllers;

use Prometheus\RenderTextFormat;
use Prometheus\CollectorRegistry;

class MetricsController extends Controller
{
    public function export()
{
    $registry = \App\PrometheusExporter::getRegistry();

    $renderer = new RenderTextFormat();
    $metrics = $renderer->render($registry->getMetricFamilySamples());

    return response($metrics, 200)
        ->header('Content-Type', RenderTextFormat::MIME_TYPE);
}

}
