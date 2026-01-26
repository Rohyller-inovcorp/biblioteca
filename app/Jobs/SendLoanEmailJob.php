<?php

namespace App\Jobs;

use App\Mail\LoanCreatedMail;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class SendLoanEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Loan $loan,
        public User $user,
        public bool $isAdmin = false
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->user->email)
                ->send(new LoanCreatedMail($this->loan, $this->user, $this->isAdmin));
            Log::info("Email enviado para {$this->user->email} sobre a requisição {$this->loan->sequential_number}");
        } catch (\Exception $e) {
            Log::error("Error enviando email a {$this->user->email}: " . $e->getMessage());
        }
    }
}
