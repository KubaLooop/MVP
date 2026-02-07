<?php

namespace App\Services;

use App\Models\User;
use App\Models\Absence;
use App\Enums\AbsenceStatus;
use App\Enums\AbsenceType;
use App\Events\AbsenceStatusChanged;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class AbsenceService
{
    public function __construct(
        protected WorkDaysCalc $calc
    ) {}

    public function createRequest(User $user, array $data): Absence
    {
        $start = Carbon::parse($data['dateFrom']);
        $end = Carbon::parse($data['dateTo']);

        // Prace s hodinami
        if ($start->equalTo($end)) {
            // budto jsou hodiny zadane nebo defaultne 8h
            $hours = $data['hours'] ?? 8;
        } else {
            // U vice dni se spocitaji workdays a vynasobi 8h
            $hours = $this->calc->calculateHours($start, $end);
        }

        // Tvorba zaznamu
        return Absence::create([
            'userID' => $user->id,
            'type' => $data['type'],
            'dateFrom' => $start,
            'dateTo' => $end,
            'hours' => $hours,
            'status' => AbsenceStatus::PENDING,
        ]);
    }

    //Schvaleni
    public function approveRequest(Absence $request): Absence
    {
        if ($request->status !== AbsenceStatus::PENDING) {
            throw new Exception("Lze schválit pouze čekající žádosti.");
        }

        $request->update([
            'status' => AbsenceStatus::APPROVED,
        ]);

        event(new AbsenceStatusChanged($request));
        return $request;
    }

    // Zamitnuti
    public function rejectRequest(Absence $request): Absence
    {
        $request->update([
            'status' => AbsenceStatus::REJECTED,
        ]);

        event(new AbsenceStatusChanged($request));
        return $request;
    }

    // Uprava zadosti (mozna jen pro managera)
    public function updateRequest(Absence $request, array $data): Absence
    {
        // Prepocet hodin
        $start = isset($data['dateFrom']) ? Carbon::parse($data['dateFrom']) : $request->dateFrom;
        $end = isset($data['dateTo']) ? Carbon::parse($data['dateTo']) : $request->dateTo;
        
        if (isset($data['hours'])) {
            $hours = $data['hours'];
        } else {
            // pokud se nezdaly hodiny, tak je budto nebyla potreba menit nebo se musi prepocitat
             if ($start->equalTo($end)) {
                $hours = $request->hours; 
             } else {
                $hours = $this->calc->calculateHours($start, $end);
             }
        }

        $request->update([
            'dateFrom' => $start,
            'dateTo' => $end,
            'hours' => $hours,
            'type' => $data['type'] ?? $request->type,
        ]);

        event(new AbsenceStatusChanged($request));
        return $request;
    }

    // Zruseni zadosti
    public function cancelRequest(Absence $request): Absence
    {
        $request->update([
            'status' => AbsenceStatus::CANCELLED
        ]);

        event(new AbsenceStatusChanged($request));
        return $request;
        
    }
    // Export mesice
    public function getMonthlyReport(int $year, int $month): array
    {
        // Najdeme vsechny schvalene dny daneho mesice
        $absences = Absence::where('status', AbsenceStatus::APPROVED)
            ->where(function ($query) use ($year, $month) {
                // Filtr intervalu
                $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
                $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
                
                $query->whereBetween('dateFrom', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('dateTo', [$startOfMonth, $endOfMonth]);
            })
            ->with('user')
            ->get();

        $totalHours = 0;
        $reportData = [];

        foreach ($absences as $absence) {
            // Z kazdeho zaznamu ulozime do reportu a secteme hodiny
            
            $reportData[] = [
                'employee' => $absence->user->name,
                'type' => $absence->type,
                'from' => $absence->dateFrom->format('Y-m-d'),
                'to' => $absence->dateTo->format('Y-m-d'),
                'hours' => $absence->hours,
            ];
            
            $totalHours += $absence->hours;
        }

        return [
            'period' => "$month/$year",
            'total_vacation_hours' => $totalHours,
            'records' => $reportData
        ];
    }

}