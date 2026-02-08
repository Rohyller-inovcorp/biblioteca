<?php
namespace App\Observers;

use App\Models\Loan;
use App\Models\BookAlert;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookAvailableMail; 
use Illuminate\Support\Facades\Log;
class LoanObserver
{
    public function updated(Loan $loan)
    {
        if ($loan->wasChanged('actual_return_date') && !is_null($loan->actual_return_date)) {
            
            $book = $loan->book;

            $alerts = BookAlert::where('book_id', $book->id)
                                ->where('is_notified', false)
                                ->with('user') 
                                ->get();

            foreach ($alerts as $alert) {
                Mail::to($alert->user->email)->send(new BookAvailableMail($alert->user, $book));

                $alert->update(['is_notified' => true]);
            }
        }
    }
}