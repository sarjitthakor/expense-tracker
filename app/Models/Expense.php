<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\ExpenseCreated;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'date',
        'is_recurring',
        'recurring_frequency',
        'currency'
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected $dispatchesEvents = [
        'created' => ExpenseCreated::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
