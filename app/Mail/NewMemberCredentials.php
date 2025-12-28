<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMemberCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $community;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $password, $community = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->community = $community;
    }

    public function build()
    {
        $subject = 'Vos identifiants de connexion';
        $mailMessage = (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subject)
            ->greeting('Bienvenue sur la plateforme !')
            ->line('Voici vos identifiants de connexion :')
            ->line('Email : ' . $this->user->email)
            ->line('Mot de passe : ' . $this->password)
            ->action('Se connecter', url('/login'));

        return $this
            ->subject($subject)
            ->html((string) $mailMessage->render());
    }
}
