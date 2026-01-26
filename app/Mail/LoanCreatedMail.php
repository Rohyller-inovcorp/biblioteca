<?php

namespace App\Mail;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Mail\Mailable;

class LoanCreatedMail extends Mailable
{
    public Loan $loan;
    public User $user;
    public bool $isAdmin;

    public function __construct(Loan $loan, User $user, bool $isAdmin = false)
    {
        $this->loan = $loan;
        $this->user = $user;
        $this->isAdmin = $isAdmin;
    }

    public function build()
    {
        return $this->subject($this->isAdmin ? 'Novo empréstimo registado' : 'Confirmação do seu empréstimo')
                    ->view('emails.loan-created');
    }
}
