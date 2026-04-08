<?php

namespace App\Services;

/**
 *  NPV CALCULATOR SERVICE  (v2)
 *  Layer  : Business Logic / Service Layer
 */
class NpvCalculatorService
{
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
        if ($npv > 0)  return ['label' => 'Worthy Investment / Accepted',      'class' => 'feasible',   'feasible' => true];
        if ($npv == 0) return ['label' => 'Break Even Investment',     'class' => 'breakeven',  'feasible' => true];
        return                ['label' => 'Bad investment / Declined', 'class' => 'infeasible', 'feasible' => false];
    }
}