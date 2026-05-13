<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailService
{
    protected string $apiKey;
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        $this->apiKey    = config('services.brevo.key');
        $this->fromEmail = config('mail.from.address');
        $this->fromName  = config('mail.from.name');
    }

    /**
     * Send a transactional email via Brevo API v3.
     */
    public function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender'      => ['email' => $this->fromEmail, 'name' => $this->fromName],
            'to'          => [['email' => $toEmail, 'name' => $toName]],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ]);

        return $response->successful();
    }
}
