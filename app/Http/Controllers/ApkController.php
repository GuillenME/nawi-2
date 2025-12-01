<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApkController extends Controller
{
    /**
     * Descargar el APK principal
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        // Buscar primero app-release.apk, luego app.apk
        $apkPath = public_path('apk/app-release.apk');
        
        if (!file_exists($apkPath)) {
            $apkPath = public_path('apk/app.apk');
        }
        
        if (!file_exists($apkPath)) {
            abort(404, 'APK no encontrado. Por favor, sube el archivo APK en public/apk/app-release.apk o public/apk/app.apk');
        }
        
        return response()->download($apkPath, 'nawi-app.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }

    /**
     * Descargar APK con nombre personalizado
     * 
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCustom($filename)
    {
        // Validar que el archivo tenga extensión .apk
        if (!preg_match('/\.apk$/', $filename)) {
            abort(400, 'Formato de archivo inválido. Solo se permiten archivos .apk');
        }
        
        // Prevenir path traversal
        $filename = basename($filename);
        
        $apkPath = public_path('apk/' . $filename);
        
        if (!file_exists($apkPath)) {
            abort(404, 'APK no encontrado');
        }
        
        return response()->download($apkPath, $filename, [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }

    /**
     * Obtener información del APK (versión, tamaño, etc.)
     * Útil para verificar actualizaciones desde la app móvil
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function info()
    {
        // Buscar primero app-release.apk, luego app.apk
        $apkPath = public_path('apk/app-release.apk');
        
        if (!file_exists($apkPath)) {
            $apkPath = public_path('apk/app.apk');
        }
        
        if (!file_exists($apkPath)) {
            return response()->json([
                'success' => false,
                'message' => 'APK no encontrado'
            ], 404);
        }
        
        $fileSize = filesize($apkPath);
        $lastModified = filemtime($apkPath);
        
        return response()->json([
            'success' => true,
            'data' => [
                'filename' => 'nawi-app.apk',
                'size' => $fileSize,
                'size_mb' => round($fileSize / 1024 / 1024, 2),
                'last_modified' => date('Y-m-d H:i:s', $lastModified),
                'download_url' => route('download.apk'),
            ]
        ]);
    }
}

