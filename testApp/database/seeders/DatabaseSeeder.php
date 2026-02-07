<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Absence;
use App\Enums\AbsenceType;   
use App\Enums\AbsenceStatus; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
// Pouze pro testování, generováno LLM 
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Vytvoření MANAŽERA (Boss)
        $boss = User::create([
            'name' => 'Vedoucí 1',
            'email' => 'manager1@firma.cz',
            'password' => Hash::make('secret'),
            'role' => 'manager',
        ]);

        // 2. Vytvoření ZAMĚSTNANCŮ (Pepa a Jana)
        $pepa = User::create([
            'name' => 'Zaměstnanec 1',
            'email' => 'employee1@firma.cz',
            'password' => Hash::make('secret'),
            'role' => 'employee',
        ]);

        $jana = User::create([
            'name' => 'Zaměstnanec 2',
            'email' => 'employee2@firma.cz',
            'password' => Hash::make('secret'),
            'role' => 'employee',
        ]);

        // 3. Absence pro Pepu
        // A) Čekající dovolená (Budoucí) - 5 dní (40h)
        Absence::create([
            'userID' => $pepa->id,
            'type' => AbsenceType::VACATION,
            'dateFrom' => '2026-08-10',
            'dateTo' => '2026-08-14',
            'hours' => 40.00,
            'status' => AbsenceStatus::PENDING,
        ]);

        // B) Schválená nemoc (Minulost) - 3 dny (24h)
        Absence::create([
            'userID' => $pepa->id,
            'type' => AbsenceType::SICK,
            'dateFrom' => '2026-02-01',
            'dateTo' => '2026-02-03',
            'hours' => 24.00,
            'status' => AbsenceStatus::APPROVED, 
            'googleCalendarID' => 'gcal_mock_id_12345', 
        ]);

        // 4. Absence pro Janu
        // A) Zamítnuté OČR (Minulost) - 1 den (8h)
        Absence::create([
            'userID' => $jana->id,
            'type' => AbsenceType::OCR,
            'dateFrom' => '2026-01-20',
            'dateTo' => '2026-01-20',
            'hours' => 8.00,
            'status' => AbsenceStatus::REJECTED,
        ]);

        // B) Schválená dovolená v srpnu (pro export reportu) - 2 dny (16h)
        Absence::create([
            'userID' => $jana->id,
            'type' => AbsenceType::VACATION,
            'dateFrom' => '2026-08-20',
            'dateTo' => '2026-08-21',
            'hours' => 16.00,
            'status' => AbsenceStatus::APPROVED,
            'googleCalendarID' => 'gcal_mock_id_67890',
        ]);
        
        // 5. Absence pro Manažera (i šéf má dovolenou)
        Absence::create([
            'userID' => $boss->id,
            'type' => AbsenceType::VACATION,
            'dateFrom' => '2026-12-24',
            'dateTo' => '2026-12-31',
            'hours' => 48.00, // Zhruba
            'status' => AbsenceStatus::APPROVED,
        ]);
    }
}
