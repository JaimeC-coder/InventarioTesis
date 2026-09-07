<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSalesCycle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DispatchSalesCycleJobs extends Command
{
    protected $signature = 'sales:dispatch-cycle-jobs';

    protected $description = 'Despacha a la cola sales-cycles un job por cada ciclo de ventas/reposición del año 2026, con reintento automático ante cortes de Azure SQL.';

    public function handle(): int
    {
        $this->waitForDatabase();

        $dates = [];
        $currentDate = Carbon::create(2026, 1, 1, 9, 0, 0);
        $endOfYear = Carbon::create(2026, 12, 31, 23, 59, 59);
        while ($currentDate->lte($endOfYear)) {
            $dates[] = $currentDate->copy();
            $currentDate->addDays(random_int(3, 4));
        }

        $jobs = [];
        foreach ($dates as $index => $date) {
            $jobs[] = new ProcessSalesCycle($index + 1, $date->toDateTimeString());
        }

        $batch = Bus::batch($jobs)
            ->name('sales-cycles-2026')
            ->onQueue('sales-cycles')
            ->catch(function (Throwable $e): void {
                Log::error('Batch de ciclos de ventas con fallas: ' . $e->getMessage());
            })
            ->dispatch();

        $this->info(sprintf('Despachado batch %s: %d ciclos (jobs) a la cola sales-cycles.', $batch->id, count($jobs)));
        $this->warn('IMPORTANTE: cada ciclo calcula su propio correlativo leyendo MAX(correlativo) al iniciar. Debe procesarse con UN SOLO worker (sin concurrencia), o dos ciclos podrian generar el mismo correlativo.');
        $this->info('Para procesarlos: php artisan queue:work redis --queue=sales-cycles');
        $this->info('Para ver el progreso: SELECT name, total_jobs, pending_jobs, failed_jobs FROM job_batches WHERE name = \'sales-cycles-2026\';');

        return self::SUCCESS;
    }

    /**
     * Azure SQL Serverless se pausa cuando está inactivo y tarda unos
     * segundos en despertar. Este comando corre una sola vez, así que hay
     * que esperar aquí manualmente antes de la primera consulta real.
     */
    private function waitForDatabase(int $maxAttempts = 10): void
    {
        $delaysInSeconds = [3, 5, 8, 10, 15, 15, 20, 20, 30];

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::select('select 1 as ping');
                return;
            } catch (Throwable $exception) {
                DB::disconnect();

                if ($attempt === $maxAttempts) {
                    throw $exception;
                }

                $wait = $delaysInSeconds[$attempt - 1] ?? 30;
                $this->warn(sprintf(
                    'Intento %d/%d: la base de datos no respondió (%s). Reintentando en %ds...',
                    $attempt,
                    $maxAttempts,
                    $exception->getMessage(),
                    $wait
                ));
                sleep($wait);
            }
        }
    }
}
