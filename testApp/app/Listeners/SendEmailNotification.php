<?php

namespace App\Listeners;

use App\Events\AbsenceStatusChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


class SendEmailNotification implements ShouldQueue // pro asynchronni odeslani
{
    use InteractsWithQueue;

    public function handle(AbsenceStatusChanged $event): void
    {
        $absence = $event->absence;
        $user = $absence->user; 

        $subject = "Změna stavu absence: " . $absence->status->label();
        
        $message = "Ahoj {$user->name},\n\n";
        $message .= "Stav tvé žádosti o {$absence->type->label()} byl změněn na: {$absence->status->label()}.\n";
        
        if ($absence->dateFrom != $absence->dateTo) {
             $message .= "Termín: {$absence->dateFrom->format('d.m.Y')} - {$absence->dateTo->format('d.m.Y')}\n";
        }
        else {
             $message .= "Termín: {$absence->dateFrom->format('d.m.Y')}\n";
             $message .= "Hodiny: {$absence->hours}\n";
        }
        
        $message .= "\nVytvořeno automaticky pomocí MVP systému.";

        // Odeslání
        Mail::raw($message, function ($mail) use ($user, $subject) {
            $mail->to($user->email)
                 ->subject($subject);
        });
    }
}
