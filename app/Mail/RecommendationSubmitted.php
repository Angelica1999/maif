<?php

namespace App\Mail;

use App\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecommendationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Recommendation $recommendation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Recommendation Has Been Received – #' . $this->recommendation->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recommendation-submitted',
        );
    }
}