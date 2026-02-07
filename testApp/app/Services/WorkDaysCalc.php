<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WorkDaysCalc
{
    // pomocna funkce pro praci pouze s pracovnimi dny
    public function calculateDays(Carbon $startDate, Carbon $endDate): int
    {
        
        $period = CarbonPeriod::create($startDate, $endDate);
        
        $workingDays = 0;

        foreach ($period as $date) {
            // skipujeme vikendy
            if (! $date->isWeekend()) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    // vrati realny pocet hodin
    public function calculateHours(Carbon $startDate, Carbon $endDate): int
    {
        return $this->calculateDays($startDate, $endDate) * 8;
    }
}
