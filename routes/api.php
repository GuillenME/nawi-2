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

// Rutas protegidas con Passport
Route::middleware('auth:api')->group(function () {
    // Autenticación
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // Pasajeros (protegidas)
    Route::apiResource('pasajeros', PasajeroController::class);

    // Taxistas (protegidas)
    Route::get('/taxista/me', [TaxistaController::class, 'me']);
    Route::post('/taxista/upload-matricula', [TaxistaController::class, 'uploadMatricula']);
    Route::post('/taxista/upload-licencia', [TaxistaController::class, 'uploadLicencia']);
    Route::get('/taxista/documents', [TaxistaController::class, 'getDocuments']);
});

// Rutas para Géneros (públicas)
Route::apiResource('generos', GeneroController::class);

// Rutas para Idiomas (públicas)
Route::apiResource('idiomas', IdiomaController::class);

// Rutas para Matrículas (públicas)
Route::apiResource('matriculas', MatriculaController::class);

// Rutas para Licencias (públicas)
Route::apiResource('licencias', LicenciaController::class);
