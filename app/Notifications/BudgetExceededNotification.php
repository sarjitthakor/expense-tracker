<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetExceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $categoryName,
        public float $budgetLimit,
        public float $totalExpenses,
        public float $percentageUsed,
        public bool $warning = false
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->warning ? 'Budget Warning' : 'Budget Exceeded';
        $subject = "{$type}: {$this->categoryName}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!");

        if ($this->warning) {
            $message->line("⚠️ **Budget Warning** for **{$this->categoryName}**")
                ->line("You've spent " . number_format($this->percentageUsed, 1) . "% of your budget.")
                ->line("Budget Limit: ₹" . number_format($this->budgetLimit, 2))
                ->line("Total Spent: ₹" . number_format($this->totalExpenses, 2))
                ->line("Remaining: ₹" . number_format($this->budgetLimit - $this->totalExpenses, 2));
        } else {
            $message->line("🔴 **Budget Exceeded** for **{$this->categoryName}**")
                ->line("You have exceeded your budget limit!")
                ->line("Budget Limit: ₹" . number_format($this->budgetLimit, 2))
                ->line("Total Spent: ₹" . number_format($this->totalExpenses, 2))
                ->line("Overspent: ₹" . number_format($this->totalExpenses - $this->budgetLimit, 2));
        }

        return $message
            ->action('View Dashboard', route('filament.admin.index'))
            ->salutation('Best regards, Expense Tracker Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->warning ? 'warning' : 'danger',
            'category' => $this->categoryName,
            'budget_limit' => $this->budgetLimit,
            'total_expenses' => $this->totalExpenses,
            'percentage_used' => $this->percentageUsed,
            'message' => $this->warning
                ? "Budget warning for {$this->categoryName}: {$this->percentageUsed}% used"
                : "Budget exceeded for {$this->categoryName}!",
        ];
    }
}
