<?php

namespace App\Livewire\Expense;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;

class ExpenseList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔍 Filters
    public $search = '';
    public $category = '';
    public $fromDate = '';
    public $toDate = '';

    // 🔄 Real-time refresh listener
    protected $listeners = ['expenseAdded' => '$refresh'];

    // 🔄 Reset pagination when filters change
    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); }
    public function updatingFromDate() { $this->resetPage(); }
    public function updatingToDate() { $this->resetPage(); }

    // 🔄 Clear all filters
    public function clearFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->fromDate = '';
        $this->toDate = '';
        $this->resetPage();
        session()->flash('message', 'Filters cleared.');
    }

    // 🗑️ Delete Expense
    public function delete($id)
    {
        $expense = Expense::where('user_id', auth()->id())->findOrFail($id);
        $expense->delete();

        session()->flash('message', 'Expense deleted successfully.');
    }

    public function render()
    {
        $query = Expense::with('category') // ✅ Eager loading
            ->where('user_id', auth()->id());

        // 🔍 Search
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // 📂 Category filter
        if ($this->category) {
            $query->where('category_id', $this->category);
        }

        // 📅 Date filters
        if ($this->fromDate) {
            $query->whereDate('date', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('date', '<=', $this->toDate);
        }

        $expenses = $query->latest()->paginate(10);

        return view('livewire.expense-list', [
            'expenses' => $expenses
        ]);
    }
}
