<?php

namespace App\Services;

/**
 * ============================================================
 *  NPV CALCULATOR SERVICE  (v2 — dengan DB support)
 *  Layer  : Business Logic / Service Layer
 *  Tugas  : Menjalankan semua perhitungan matematis NPV.
 *           TIDAK tahu tentang database — itu urusan Repository.
 * ============================================================
 */
class NpvCalculatorService
{
    /**
     * Hitung NPV lengkap beserta rincian per tahun.
     *
     * Rumus NPV:
     *   NPV = -C₀ + Σ [ CFₜ / (1 + r)^t ]   untuk t = 1..n
     */
    public function calculate(float $initialInvestment, float $discountRate, array $cashFlows): array
    {
        $rate = $discountRate / 100;

        $yearlyDetails     = [];
        $totalPresentValue = 0;

        foreach ($cashFlows as $index => $cashFlow) {
            $period         = $index + 1;
            $discountFactor = pow(1 + $rate, $period);
            $presentValue   = $cashFlow / $discountFactor;

            $totalPresentValue += $presentValue;

            $yearlyDetails[] = [
                'year'            => $period,
                'cash_flow'       => (float) $cashFlow,
                'discount_factor' => $discountFactor,
                'present_value'   => $presentValue,
            ];
        }

        $npv      = $totalPresentValue - $initialInvestment;
        $decision = $this->makeDecision($npv);

        return [
            'initial_investment'  => $initialInvestment,
            'discount_rate'       => $discountRate,
            'rate_decimal'        => $rate,
            'yearly_details'      => $yearlyDetails,
            'total_present_value' => $totalPresentValue,
            'npv'                 => $npv,
            'decision'            => $decision['label'],
            'decision_class'      => $decision['class'],
            'is_feasible'         => $decision['feasible'],
        ];
    }

    private function makeDecision(float $npv): array
    {
        if ($npv > 0)  return ['label' => 'Investasi LAYAK / DITERIMA',      'class' => 'feasible',   'feasible' => true];
        if ($npv == 0) return ['label' => 'Investasi BREAK EVEN (Impas)',     'class' => 'breakeven',  'feasible' => true];
        return                ['label' => 'Investasi TIDAK LAYAK / DITOLAK', 'class' => 'infeasible', 'feasible' => false];
    }
}