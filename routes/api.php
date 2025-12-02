<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\IdiomaController;
use App\Http\Controllers\PasajeroController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\LicenciaController;
use App\Http\Controllers\TaxistaController;
use App\Http\Controllers\PasajeroViajeController;
use App\Http\Controllers\TaxistaViajeController;
use App\Http\Controllers\SistemaViajeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuscripcionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticación (públicas)
Route::post('/register/pasajero', [AuthController::class, 'registerPasajero']);
Route::post('/register/taxista', [AuthController::class, 'registerTaxista']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas de recuperación de contraseña (públicas)
// Endpoint principal: solicitar recuperación de contraseña
Route::post('/password/forgot', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkApi']);
Route::post('/password/email', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkApi']); // Alias para compatibilidad

// Endpoint principal: restablecer contraseña con token
Route::post('/password/reset', [App\Http\Controllers\PasswordResetController::class, 'resetPasswordApi']);

// Endpoint adicional: verificar token de recuperación
Route::post('/password/verify-token', [App\Http\Controllers\PasswordResetController::class, 'verifyToken']);

// Endpoint público para crear roles básicos (útil para primera instalación)
Route::post('/roles/ensure-defaults', [RoleController::class, 'ensureDefaults']);

// Rutas protegidas con Passport
Route::middleware('auth:api')->group(function () {
    // Autenticación
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/usuario/{userId}', [AuthController::class, 'getUsuario']);
    Route::put('/usuario/perfil', [AuthController::class, 'updatePerfil']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // Pasajeros (protegidas)
    Route::apiResource('pasajeros', PasajeroController::class);

    // ========== ENDPOINTS PARA PASAJEROS ==========
    Route::prefix('pasajero')->group(function () {
        Route::post('/crear-viaje', [PasajeroViajeController::class, 'crearViaje']);
        Route::get('/mis-viajes', [PasajeroViajeController::class, 'misViajes']);
        Route::post('/cancelar-viaje/{viajeId}', [PasajeroViajeController::class, 'cancelarViaje']);
        Route::post('/calificar-viaje/{viajeId}', [PasajeroViajeController::class, 'calificarViaje']);
    });

    // Taxistas (protegidas)
    Route::get('/taxista/me', [TaxistaController::class, 'me']);
    Route::post('/taxista/upload-matricula', [TaxistaController::class, 'uploadMatricula']);
    Route::post('/taxista/upload-licencia', [TaxistaController::class, 'uploadLicencia']);
    Route::get('/taxista/documents', [TaxistaController::class, 'getDocuments']);

    // ========== ENDPOINTS PARA TAXISTAS ==========
    Route::prefix('taxista')->group(function () {
        Route::get('/viajes-disponibles', [TaxistaViajeController::class, 'viajesDisponibles'])->middleware('suscripcion.activa');
        Route::get('/mis-viajes', [TaxistaViajeController::class, 'misViajes'])->middleware('suscripcion.activa');
        Route::post('/aceptar-viaje/{viajeId}', [TaxistaViajeController::class, 'aceptarViaje'])->middleware('suscripcion.activa');
        Route::post('/rechazar-viaje/{viajeId}', [TaxistaViajeController::class, 'rechazarViaje'])->middleware('suscripcion.activa');
        Route::post('/completar-viaje/{viajeId}', [TaxistaViajeController::class, 'completarViaje'])->middleware('suscripcion.activa');
    });

    // ========== ENDPOINTS DEL SISTEMA ==========
    Route::prefix('viaje')->group(function () {
        Route::get('/estado/{viajeId}', [SistemaViajeController::class, 'estadoViaje']);
        Route::post('/actualizar-ubicacion/{viajeId}', [SistemaViajeController::class, 'actualizarUbicacion']);
    });

    // ========== GESTIÓN DE ROLES ==========
    Route::apiResource('roles', RoleController::class);

    // ========== SUSCRIPCIONES ==========
    Route::prefix('suscripcion')->group(function () {
        Route::get('/mi-suscripcion', [SuscripcionController::class, 'miSuscripcion']);
        Route::get('/historial', [SuscripcionController::class, 'historial']);
        Route::post('/crear', [SuscripcionController::class, 'crearSuscripcion']);
        Route::post('/confirmar-pago/{suscripcionId}', [SuscripcionController::class, 'confirmarPago']);
    });
});

// Webhook de Stripe (sin autenticación, usa firma de Stripe)
Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);

// Rutas para Géneros (públicas)
Route::apiResource('generos', GeneroController::class);

// Rutas para Idiomas (públicas)
Route::apiResource('idiomas', IdiomaController::class);

// Rutas para Matrículas (públicas)
Route::apiResource('matriculas', MatriculaController::class);

// Rutas para Licencias (públicas)
Route::apiResource('licencias', LicenciaController::class);
