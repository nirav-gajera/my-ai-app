<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to '.config('app.name', 'My AI App'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-user',
            with: [
                'userName' => $this->user->name,
                'appName' => config('app.name', 'My AI App'),
                'appUrl' => config('app.url'),
                'loginUrl' => url('/login'),
                'knowledgeUrl' => url('/knowledge'),
                'conversationUrl' => url('/conversations'),
            ],
        );
    }
}
