<?php

namespace App\Listeners;

use App\Events\AbsenceStatusChanged;
use App\Enums\AbsenceStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncToGoogleCalendar
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AbsenceStatusChanged $event): void
{
    $absence = $event->absence;
    
    // Pri schvaleni se vytvori udalost
    if ($absence->status === AbsenceStatus::APPROVED) {
        $calendarService = app(\App\Services\GoogleCalendarService::class);
        $eventId = $calendarService->createEvent($absence);
        
        // ulozime smyslene ID do DB (ready na ID od API)
        $absence->update(['googleCalendarID' => $eventId]);
    }
    
    // Pokud se rezervace zrusi a uz byla vytvorena, tak ji "smazeme"
    if ($absence->status === AbsenceStatus::CANCELLED && $absence->googleCalendarID) {
        $calendarService = app(\App\Services\GoogleCalendarService::class);
        $calendarService->deleteEvent($absence->googleCalendarID);
        
        $absence->update(['googleCalendarID' => null]);
    }
}
}
