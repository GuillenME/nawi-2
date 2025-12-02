<?php

namespace App\Console\Commands;

use App\Jobs\VerificarSuscripcionesVencidas;
use Illuminate\Console\Command;

class VerificarSuscripcionesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscripciones:verificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y desactiva suscripciones vencidas o no pagadas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando suscripciones vencidas...');
        
        VerificarSuscripcionesVencidas::dispatch();
        
        $this->info('Proceso de verificación iniciado.');
        
        return Command::SUCCESS;
    }
}
