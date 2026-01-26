<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Loan;
use App\Models\User;
use App\Mail\LoanReminderMail;
class SendLoanReminderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Loan $loan,
        public User $user,)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        try {
            Mail::to($this->user->email)->send(new LoanReminderMail($this->loan, $this->user));
            Log::info("Email enviado para {$this->user->email} sobre a requisição {$this->loan->sequential_number}");
        } catch (\Exception $e) {
            Log::error("Error enviando email a {$this->user->email}: " . $e->getMessage());
        }
    }
}
