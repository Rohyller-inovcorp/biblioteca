<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition()
    {
        return [
            'sequential_number'    => 'REQ-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id'              => User::factory(),
            'book_id'              => Book::factory(),
            'loan_date'            => now(),
            'expected_return_date' => now()->addDays(5),
            'actual_return_date'   => null,
            'days_elapsed'         => null,
        ];
    }
}
