<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;

class ExpenseManager extends Component
{
    public $category = '';
    public $fromDate = '';
    public $toDate = '';
    public $minAmount = '';
    public $maxAmount = '';

    public function delete($id)
    {
        Expense::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function render()
    {
        $query = Expense::query()
            ->where('user_id', auth()->id());

        // 📂 Category filter
        if ($this->category) {
            $query->where('category_id', $this->category);
        }

        // 📅 Date range
        if ($this->fromDate) {
            $query->whereDate('expense_date', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('expense_date', '<=', $this->toDate);
        }

        // 💰 Amount range
        if ($this->minAmount) {
            $query->where('amount', '>=', $this->minAmount);
        }

        if ($this->maxAmount) {
            $query->where('amount', '<=', $this->maxAmount);
        }

        $expenses = $query->latest()->get();

        return view('livewire.expense-manager', compact('expenses'));
    }
}