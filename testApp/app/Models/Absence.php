<?php

namespace App\Models;

use App\Enums\AbsenceType;
use App\Enums\AbsenceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    protected $table = 'absence';
    protected $fillable = [
        'userID', 'type', 'dateFrom', 'dateTo', 'hours', 'status', 'googleCalendarID'
    ];

    protected $casts = [
        'type' => AbsenceType::class,
        'dateFrom' => 'date',
        'dateTo' => 'date',
        'status' => AbsenceStatus::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

}
