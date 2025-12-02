<?php

namespace App\Mail;

use App\Models\Suscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuscripcionRecordatorioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $suscripcion;
    public $taxista;
    public $diasRestantes;

    /**
     * Create a new message instance.
     */
    public function __construct(Suscripcion $suscripcion, int $diasRestantes)
    {
        $this->suscripcion = $suscripcion;
        $this->taxista = $suscripcion->taxista;
        $this->diasRestantes = $diasRestantes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio: Tu suscripción vence pronto - NAWI',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.suscripcion-recordatorio',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
