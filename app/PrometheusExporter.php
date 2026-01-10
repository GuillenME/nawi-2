<?php

namespace App;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;

class PrometheusExporter
{
    private static $registry = null;

    public static function getRegistry()
    {
        if (self::$registry === null) {

            $storage = env('PROMETHEUS_STORAGE', 'memory');

            if ($storage === 'apc') {
                self::$registry = new CollectorRegistry(new APC());
            } else {
                self::$registry = new CollectorRegistry(new InMemory());
            }
        }

        return self::$registry;
    }
}
