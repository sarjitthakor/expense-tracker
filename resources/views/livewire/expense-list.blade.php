<div class="container mt-4">

    <h3>💰 Expense List</h3>

    <!-- ✅ Flash Message -->
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- 🔍 Filters Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">🔎 Search Description</label>
                    <input type="text" class="form-control" wire:model.live="search" placeholder="Enter description...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">📂 Category</label>
                    <select class="form-select" wire:model.live="category">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Category::where('user_id', auth()->id())->get() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">📅 From Date</label>
                    <input type="date" class="form-control" wire:model.live="fromDate">
                </div>

                <div class="col-md-2">
                    <label class="form-label">📅 To Date</label>
                    <input type="date" class="form-control" wire:model.live="toDate">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" wire:click="dispatch('clearFilters')">
                        🔄 Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 💰 Expenses Table -->
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th width="12%">💰 Amount</th>
                    <th width="15%">📂 Category</th>
                    <th width="15%">📅 Date</th>
                    <th width="35%">📝 Description</th>
                    <th width="10%">🔁 Recurring</th>
                    <th width="13%">⚙️ Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>
                        <span class="badge bg-primary">₹ {{ number_format($expense->amount, 2) }}</span>
                    </td>
                    <td>
                        @if($expense->category)
                            <span class="badge bg-info">{{ $expense->category->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        {{ $expense->date ? $expense->date->format('d M Y') : '-' }}
                    </td>
                    <td>{{ Str::limit($expense->description, 50) }}</td>
                    <td>
                        @if($expense->is_recurring)
                            <span class="badge bg-warning">{{ ucfirst($expense->recurring_frequency) }}</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="#" class="btn btn-primary" title="View">👁️</a>
                            <a href="#" class="btn btn-warning" title="Edit">✏️</a>
                            <button type="button" class="btn btn-danger" 
                                wire:click="delete({{ $expense->id }})"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                title="Delete">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        📭 No expenses found. Start tracking!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 📄 Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $expenses->links() }}
    </div>

</div>

<style>
    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
