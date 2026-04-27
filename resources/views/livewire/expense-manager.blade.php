<div class="p-4">

    <!-- 🔍 Filters -->
    <div class="grid grid-cols-5 gap-4 mb-4">

        <input type="text" wire:model="category" placeholder="Category ID" class="border p-2">

        <input type="date" wire:model="fromDate" class="border p-2">
        <input type="date" wire:model="toDate" class="border p-2">

        <input type="number" wire:model="minAmount" placeholder="Min ₹" class="border p-2">
        <input type="number" wire:model="maxAmount" placeholder="Max ₹" class="border p-2">
    </div>

    <!-- 📊 Table -->
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Category</th>
                <th class="p-2">Amount</th>
                <th class="p-2">Date</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td class="p-2">{{ $expense->category->name }}</td>
                <td class="p-2">₹{{ $expense->amount }}</td>
                <td class="p-2">{{ $expense->expense_date }}</td>
                <td class="p-2">
                    <button wire:click="delete({{ $expense->id }})" class="bg-red-500 text-white px-2 py-1">
                        Delete
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>