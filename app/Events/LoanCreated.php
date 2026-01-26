<?php

namespace App\Events;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanCreated
{
    use Dispatchable, SerializesModels;

    public Loan $loan;
    public User $citizen; 

    public function __construct(Loan $loan, User $citizen)
    {
        $this->loan = $loan;
        $this->citizen = $citizen;
    }
}
