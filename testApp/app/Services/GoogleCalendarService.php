<?php

namespace App\Services;

use App\Models\Absence;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    // Simulace vytvoreni udalosti a ziskani ID
    public function createEvent(Absence $absence): string
    {
        // Zde by byla logika vytvoreni udalosti v Google Calendar API
        Log::info("GOOGLE CALENDAR: Vytvářím událost pro {$absence->user->name} ({$absence->dateFrom->format('Y-m-d')})");
        
        // Vygenerujeme fake Google ID (vypadá jako reálné)
        $fakeId = 'gcal_event_' . uniqid() . '_' . time();
        
        return $fakeId;
    }

    // Simulace smazani udalosti
    public function deleteEvent(string $eventId): void
    {
        Log::info("GOOGLE CALENDAR: Mazání události ID {$eventId}");
    }
}
