<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstatusDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estatus = [
            [
                'id' => '1',
                'nombre' => 'pendiente'
            ],
            [
                'id' => '2',
                'nombre' => 'aprobado'
            ],
            [
                'id' => '3',
                'nombre' => 'rechazado'
            ]
        ];

        foreach ($estatus as $status) {
            DB::table('estatus_documentos')->insert([
                'id' => $status['id'],
                'nombre' => $status['nombre'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
