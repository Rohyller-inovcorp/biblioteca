<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use App\Jobs\SendLoanReminderJob;

class SendLoanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:loan-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar reminders de empréstimos que vencem amanhã';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tomorrow = now()->addDay()->toDateString();

        // Buscar préstamos que vencen mañana y no fueron devueltos
        $loans = Loan::whereDate('expected_return_date', $tomorrow)
            ->whereNull('actual_return_date')
            ->with('user', 'book') // importante para enviar datos al email
            ->get();

        foreach ($loans as $loan) {
            SendLoanReminderJob::dispatch($loan, $loan->user);
        }

        $this->info("Reminders despachados para " . $loans->count() . " empréstimos.");
    }
}
