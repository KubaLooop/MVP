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

    public function store(Request $request)
    {
        // Validace
        $validated = $request->validate([
            'type' => ['required', new Enum(AbsenceType::class)],
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
            'hours' => ['nullable', 'numeric', 'min:0.5', 'max:8'], // Jen pro jednodenní
        ]);

        // Pouziti AbsenceService
        $absence = $this->service->createRequest($request->user(), $validated);

        // Zpetna vazba / pripadne bude view
        return response()->json([
            'message' => 'Žádost byla vytvořena',
            'data' => $absence
        ], 201);
    }
    public function approve(Request $request, Absence $absence)
    {
        // Check if user is manager - TODO: middleware

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

    public function cancel(Request $request, Absence $absence)
    {
        $this->service->cancelRequest($absence);
        return response()->json(['message' => 'Zrušeno']);
    }
}
