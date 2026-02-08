<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
class Review extends Model
{
    protected $fillable = [
        'user_id', 
        'book_id', 
        'loan_id', 
        'rating', 
        'comment', 
        'status', 
        'rejection_reason'
    ];

    protected $casts = [
        'comment' => 'encrypted',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo {
        return $this->belongsTo(Book::class);
    }

    public function loan(): BelongsTo {
        return $this->belongsTo(Loan::class);
    }
}
