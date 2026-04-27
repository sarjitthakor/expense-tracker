<?php

namespace App\Console\Commands;

use App\Jobs\ProcessRecurringExpenses;
use Illuminate\Console\Command;

class ProcessRecurringExpensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expenses:process-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all recurring expenses and create new entries';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Processing recurring expenses...');

        try {
            ProcessRecurringExpenses::dispatch();
            $this->info('✅ Recurring expenses job dispatched successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
