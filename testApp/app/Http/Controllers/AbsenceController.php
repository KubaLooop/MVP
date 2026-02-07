<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

use App\Models\Absence;
use App\Models\User;
use App\Enums\AbsenceType;
use App\Enums\AbsenceStatus;
use App\Services\AbsenceService;

class AbsenceController extends Controller
{
    public function __construct(
        protected AbsenceService $service
    ) {}

    // Ukladani nove zadosti
    public function store(Request $request)
    {
        // Validace
        $validated = $request->validate([
            'type' => ['required', new Enum(AbsenceType::class)],
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
            'hours' => ['nullable', 'numeric'],
        ]);

        // Pouziti AbsenceService
        $absence = $this->service->createRequest($request->user(), $validated);

        // Zpetna vazba - pro jednoduchost jen JSON
        return response()->json([
            'message' => 'Žádost byla vytvořena',
            'data' => $absence
        ], 201);
    }
    // Schvaleni (od mng)
    public function approve(Request $request, Absence $absence)
    {
        try {
            $approvedAbsence = $this->service->approveRequest($absence, $request->user());
            
            return response()->json([
                'message' => 'Žádost schválena',
                'data' => $approvedAbsence
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Zamitnuti (od mng)
    public function reject(Request $request, Absence $absence)
    {

        $rejectedAbsence = $this->service->rejectRequest(
            $absence, 
            $request->user(), 
        );

        return response()->json([
            'message' => 'Žádost zamítnuta',
            'data' => $rejectedAbsence
        ]);
    }

    // Uprava zadosti
    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'dateFrom' => 'sometimes|date',
            'dateTo' => 'sometimes|date|after_or_equal:dateFrom',
            'hours' => 'nullable|numeric',
            'type' => ['sometimes', new Enum(AbsenceType::class)],
        ]);

        $updatedAbsence = $this->service->updateRequest($absence, $validated);

        return response()->json(['message' => 'Upraveno', 'data' => $updatedAbsence]);
    }

    // Zruseni
    public function cancel(Request $request, Absence $absence)
    {
        $this->service->cancelRequest($absence);
        return response()->json(['message' => 'Zrušeno']);
    }

    public function export(Request $request)
    {
        // Validace
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030', // Rozumny rozsah
            'month' => 'required|integer|min:1|max:12',
        ]);

        $data = $this->service->getMonthlyReport(
            $request->input('year'), 
            $request->input('month')
        );

        return response()->json($data);
    }
}
