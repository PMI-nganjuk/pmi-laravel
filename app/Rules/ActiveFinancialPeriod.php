<?php

namespace App\Rules;

use App\Services\OrganizationProfileService;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveFinancialPeriod implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $profile = app(OrganizationProfileService::class)->getProfile();
            
            if ($profile && $profile->financial_period_start && $profile->financial_period_end) {
                $date = Carbon::parse($value);
                $start = $profile->financial_period_start;
                $end = $profile->financial_period_end;
                
                if ($date->lt($start) || $date->gt($end)) {
                    $fail("Tanggal transaksi harus berada dalam periode aktif ({$start->format('d/m/Y')} s.d. {$end->format('d/m/Y')}).");
                }
            }
        } catch (\Throwable $e) {
            // Skip validation if profile is unavailable
        }
    }
}