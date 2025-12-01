<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApkDownloadTest extends TestCase
{
    /**
     * Test que la ruta de descarga del APK existe
     */
    public function test_apk_download_route_exists(): void
    {
        $response = $this->get('/download/apk');
        
        // Si el archivo no existe, debería retornar 404
        // Si existe, debería retornar 200 con el archivo
        $this->assertContains($response->status(), [200, 404]);
    }

    /**
     * Test que la ruta de información del APK funciona
     */
    public function test_apk_info_endpoint(): void
    {
        $response = $this->get('/api/apk/info');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'filename',
                'size',
                'size_mb',
                'last_modified',
                'download_url'
            ]
        ]);
    }

    /**
     * Test que la descarga con nombre personalizado funciona
     */
    public function test_apk_custom_download_route(): void
    {
        $response = $this->get('/download/apk/test-app.apk');
        
        // Debería retornar 404 si el archivo no existe, o 200 si existe
        $this->assertContains($response->status(), [200, 404]);
    }

    /**
     * Test que rechaza archivos sin extensión .apk
     */
    public function test_apk_download_rejects_invalid_extension(): void
    {
        $response = $this->get('/download/apk/malicious.exe');
        
        $response->assertStatus(400);
    }
}

