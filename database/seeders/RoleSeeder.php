<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => '1',
                'nombre' => 'admin'
            ],
            [
                'id' => '2',
                'nombre' => 'pasajero'
            ],
            [
                'id' => '3',
                'nombre' => 'taxista'
            ]
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'id' => $role['id'],
                'nombre' => $role['nombre'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
