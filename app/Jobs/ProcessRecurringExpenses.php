<?php

namespace App\Jobs;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRecurringExpenses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 🔄 Get all recurring expenses
        $recurringExpenses = Expense::where('is_recurring', true)->get();

        foreach ($recurringExpenses as $expense) {
            $this->processExpense($expense);
        }
    }

    /**
     * Process a single recurring expense
     */
    private function processExpense(Expense $originalExpense): void
    {
        $lastDate = $originalExpense->date;
        $frequency = $originalExpense->recurring_frequency;
        $today = Carbon::today();

        // Calculate next occurrence date based on frequency
        $nextDate = $this->calculateNextDate($lastDate, $frequency);

        // If next date is today or in the past, create the recurring expense
        if ($nextDate->lessThanOrEqualTo($today)) {
            // 💰 Create new expense entry
            Expense::create([
                'user_id' => $originalExpense->user_id,
                'category_id' => $originalExpense->category_id,
                'amount' => $originalExpense->amount,
                'description' => $originalExpense->description . ' (Recurring)',
                'date' => $nextDate,
                'is_recurring' => false, // Don't create infinite loop
                'recurring_frequency' => $originalExpense->recurring_frequency,
                'currency' => $originalExpense->currency,
            ]);

            \Log::info("Recurring expense created for user {$originalExpense->user_id}");
        }
    }

    /**
     * Calculate next occurrence date based on frequency
     */
    private function calculateNextDate(Carbon $lastDate, ?string $frequency): Carbon
    {
        $next = $lastDate->copy();

        return match ($frequency) {
            'daily' => $next->addDay(),
            'weekly' => $next->addWeek(),
            'monthly' => $next->addMonth(),
            'yearly' => $next->addYear(),
            default => $next->addMonth(),
        };
    }
}
