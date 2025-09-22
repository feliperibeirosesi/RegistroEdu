<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $toEmail;
    public $subject;
    public $data;
    public $template;
    public $fromEmail;
    public $fromName;

    public function __construct($toEmail, $subject, $template, $data = [], $fromEmail = null, $fromName = null)
    {
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->template = $template;
        $this->data = $data;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function handle()
    {
        try {
            Mail::send($this->template, $this->data, function ($message) {
                $message->to($this->toEmail)
                        ->subject($this->subject);

                if ($this->fromEmail) {
                    $message->from($this->fromEmail, $this->fromName);
                }
            });
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email: ' . $e->getMessage(), [
                'to' => $this->toEmail,
                'subject' => $this->subject,
                'template' => $this->template
            ]);
            throw $e;
        }
    }
}
