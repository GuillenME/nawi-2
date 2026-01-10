<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\Usuario;
use App\Models\Taxista;
use App\Models\EstatusDocumento;

class ControllerTest extends TestCase
{
    //use RefreshDatabase;

    /**
     * Test para PaginaController::sobreNosotros
     * Verifica que la vista 'sobre' se retorna correctamente
     */
    public function test_pagina_controller_sobre_nosotros()
    {
        $response = $this->get('/sobre-nosotros');

        $response->assertStatus(200)
                 ->assertViewIs('sobre');
    }

    /**
     * Test para WebAuthController::showLoginForm
     * Verifica que la vista de login se retorna correctamente
     */
    public function test_web_auth_controller_show_login_form()
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
                 ->assertViewIs('auth.login');
    }

    /**
     * Test para HomeController::index sin autenticación
     * Verifica que redirige a login si no está autenticado
     */
    public function test_home_controller_index_unauthenticated()
    {
        $response = $this->get('/home');

        $response->assertStatus(302)
                 ->assertRedirect('/login');
    }

}
