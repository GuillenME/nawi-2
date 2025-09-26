<?php

use App\Http\Controllers\PaginaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\TaxistaController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Auth;

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

// Rutas protegidas para taxistas
Route::middleware(['auth'])->group(function () {
    Route::get('/taxista/dashboard', [TaxistaController::class, 'dashboard'])->name('taxista.dashboard');
    Route::get('/taxista/documents', [TaxistaController::class, 'documents'])->name('taxista.documents');
    Route::post('/taxista/upload-matricula', [TaxistaController::class, 'uploadMatriculaWeb'])->name('taxista.upload.matricula');
    Route::post('/taxista/upload-licencia', [TaxistaController::class, 'uploadLicenciaWeb'])->name('taxista.upload.licencia');
});

