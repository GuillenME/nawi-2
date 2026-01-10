<?php

use App\Http\Controllers\PaginaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\TaxistaController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MetricsController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
Route::post('/perfil', [PerfilController::class, 'update'])->name('perfil.update');


Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/taxistas', [TaxistaController::class, 'index'])->name('taxistas.index');
Route::get('/sobre-nosotros', [PaginaController::class, 'sobreNosotros'])->name('sobre-nosotros');

// Rutas de autenticación web
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);

Route::get('/register/taxista', [WebAuthController::class, 'showTaxistaRegisterForm'])->name('register.taxista');
Route::post('/register/taxista', [WebAuthController::class, 'registerTaxista']);

Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Rutas de recuperación de contraseña
Route::get('/password/forgot', [App\Http\Controllers\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/password/email', [App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.update');

// Rutas protegidas para taxistas
Route::middleware(['auth'])->group(function () {
    Route::get('/taxista/dashboard', [TaxistaController::class, 'dashboard'])->name('taxista.dashboard');
    Route::get('/taxista/documents', [TaxistaController::class, 'documents'])->name('taxista.documents');
    Route::post('/taxista/upload-matricula', [TaxistaController::class, 'uploadMatriculaWeb'])->name('taxista.upload.matricula');
    Route::post('/taxista/upload-licencia', [TaxistaController::class, 'uploadLicenciaWeb'])->name('taxista.upload.licencia');

    // Rutas para gestión de taxis
    Route::get('/taxi/create', [App\Http\Controllers\TaxiController::class, 'create'])->name('taxi.create');
    Route::post('/taxi', [App\Http\Controllers\TaxiController::class, 'store'])->name('taxi.store');
    Route::get('/taxi/{id}/edit', [App\Http\Controllers\TaxiController::class, 'edit'])->name('taxi.edit');
    Route::put('/taxi/{id}', [App\Http\Controllers\TaxiController::class, 'update'])->name('taxi.update');

    // Rutas para gestión de fotos
    Route::get('/perfil/foto', function() { return view('perfil.foto'); })->name('perfil.foto');
    Route::post('/foto/upload-perfil', [App\Http\Controllers\FotoController::class, 'uploadPerfil'])->name('foto.upload.perfil');
    Route::post('/foto/eliminar-perfil', [App\Http\Controllers\FotoController::class, 'eliminarPerfil'])->name('foto.eliminar.perfil');
    Route::get('/foto/{id}', [App\Http\Controllers\FotoController::class, 'verFoto'])->name('foto.ver');
});

// Rutas de administración
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/documentos', [App\Http\Controllers\AdminController::class, 'documentos'])->name('admin.documentos');
    Route::post('/admin/aprobar-documento', [App\Http\Controllers\AdminController::class, 'aprobarDocumento'])->name('admin.aprobar.documento');
    Route::post('/admin/rechazar-documento', [App\Http\Controllers\AdminController::class, 'rechazarDocumento'])->name('admin.rechazar.documento');
    Route::get('/admin/ver-documento/{tipo}/{id}', [App\Http\Controllers\AdminController::class, 'verDocumento'])->name('admin.ver.documento');
    Route::get('/admin/descargar-documento/{tipo}/{id}', [App\Http\Controllers\AdminController::class, 'descargarDocumento'])->name('admin.descargar.documento');
});

Route::get('/offline', function () {
    return view('offline');
});

Route::get('/metrics', [MetricsController::class, 'export']);
