<?php

namespace App\Listeners;

use App\Events\LoanCreated;
use App\Jobs\SendLoanEmailJob;
use App\Models\User;

class SendLoanEmails
{
    public function handle(LoanCreated $event): void
    {
        $loan = $event->loan;
        $citizen = $event->citizen;
        SendLoanEmailJob::dispatch($loan, $citizen);

        User::where('role', 'admin')
            ->select('id','email','name')
            ->each(function ($admin) use ($loan) {
                SendLoanEmailJob::dispatch($loan, $admin, true); 
            });
    }
}
