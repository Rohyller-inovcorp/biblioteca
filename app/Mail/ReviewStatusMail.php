<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Review;
class ReviewStatusMail extends Mailable
{
    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function build()
    {
        $subject = "Atualização do seu review sobre '{$this->review->book->name}'";

        return $this->subject($subject)
                    ->view('emails.review-status');
    }
}

