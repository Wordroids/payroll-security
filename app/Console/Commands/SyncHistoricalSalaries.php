<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Http\Controllers\SalaryController;
use Carbon\Carbon;

class SyncHistoricalSalaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:sync-history {start_date} {end_date}';

    protected $description = 'Calculates and saves historical salary data into the database';

    public function handle()
    {
        $start = Carbon::parse($this->argument('start_date'))->startOfMonth();
        $end = Carbon::parse($this->argument('end_date'))->startOfMonth();

        $employees = Employee::all();
        $controller = new SalaryController();

        $this->info("Starting sync from " . $start->format('Y-m') . " to " . $end->format('Y-m'));

        $current = $start->copy();

        while ($current <= $end) {
            $monthString = $current->format('Y-m');
            $this->info("Processing Month: $monthString");

            $bar = $this->output->createProgressBar(count($employees));
            $bar->start();

            foreach ($employees as $employee) {
                try {

                    $controller->viewSlip($employee, $monthString);
                } catch (\Exception $e) {
                    $this->error("\nError for {$employee->name} in $monthString: " . $e->getMessage());
                }
                $bar->advance();
            }

            $bar->finish();
            $this->info("");
            $current->addMonth();
        }

        $this->info('Historical data sync complete!');
    }
}
