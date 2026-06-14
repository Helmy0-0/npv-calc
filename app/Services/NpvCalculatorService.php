<?php

namespace App\Services;

/**
 *  NPV CALCULATOR SERVICE  (v2)
 *  Layer  : Business Logic / Service Layer
 */
class NpvCalculatorService
{
    private const SCENARIOS = [-10, -5, 0, +5, +10];

    public function calculate(float $initialInvestment, float $discountRate, array $cashFlows): array
    {
        $rate = $discountRate / 100;

        $yearlyDetails = [];
        $totalPresentValue = 0;

        foreach ($cashFlows as $index => $cashFlow) {
            $period = $index + 1;
            $discountFactor = pow(1 + $rate, $period);
            $presentValue = $cashFlow / $discountFactor;

            $totalPresentValue += $presentValue;

            $yearlyDetails[] = [
                'year' => $period,
                'cash_flow' => (float) $cashFlow,
                'discount_factor' => $discountFactor,
                'present_value' => $presentValue,
            ];
        }

        $npv = $totalPresentValue - $initialInvestment;
        $decision = $this->makeDecision($npv);

        return [
            'initial_investment' => $initialInvestment,
            'discount_rate' => $discountRate,
            'rate_decimal' => $rate,
            'yearly_details' => $yearlyDetails,
            'total_present_value' => $totalPresentValue,
            'npv' => $npv,
            'decision' => $decision['label'],
            'decision_class' => $decision['class'],
            'is_feasible' => $decision['feasible'],

            'sensitivity_revenue' => $this->sensitivityByRevenue($initialInvestment, $discountRate, $cashFlows),
            'sensitivity_discount_rate' => $this->sensitivityByDiscountRate($initialInvestment, $discountRate, $cashFlows),
        ];
    }

    //  SENSITIVITY 1 CASH FLOW / REVENUE VARIATION
    /**
     *
     * @param  float  $initialInvestment  
     * @param  float  $discountRate       
     * @param  array  $baseCashFlows      
     * @return array  
     */
    public function sensitivityByRevenue(
        float $initialInvestment,
        float $discountRate,
        array $baseCashFlows
    ): array {
        $rate = $discountRate / 100;
        $rows = [];

        foreach (self::SCENARIOS as $pct) {
            $multiplier = 1 + ($pct / 100);
            $adjustedFlows = array_map(fn($cf) => $cf * $multiplier, $baseCashFlows);

            $avgRevenue = count($adjustedFlows) > 0
                ? array_sum($adjustedFlows) / count($adjustedFlows)
                : 0;

            // NPV with new cash flow
            $totalPV = 0;
            foreach ($adjustedFlows as $index => $cf) {
                $period = $index + 1;
                $totalPV += $cf / pow(1 + $rate, $period);
            }

            $npv = $totalPV - $initialInvestment;
            $decision = $this->makeDecision($npv);

            $rows[] = [
                'scenario' => $this->formatScenarioLabel($pct, 'revenue'),
                'pct' => $pct,
                'avg_revenue' => $avgRevenue,        
                'npv' => $npv,
                'decision' => $decision['short'],
                'is_feasible' => $decision['feasible'],
                'is_base' => $pct === 0,
                'status' => $this->resolveStatus($pct, $decision['feasible']),
            ];
        }

        return $rows;
    }

    //  SENSITIVITY 2 Discount Rate
    /**
     *
     * @param  float  $initialInvestment  
     * @param  float  $discountRate       
     * @param  array  $cashFlows          
     * @return array  
     */
    public function sensitivityByDiscountRate(
        float $initialInvestment,
        float $discountRate,
        array $cashFlows
    ): array {
        $rows = [];

        foreach (self::SCENARIOS as $pct) {
            $adjustedRate = $discountRate * (1 + ($pct / 100));   
            $adjustedRateDec = $adjustedRate / 100;                    

            $totalPV = 0;
            foreach ($cashFlows as $index => $cf) {
                $period = $index + 1;
                $totalPV += $cf / pow(1 + $adjustedRateDec, $period);
            }

            $npv = $totalPV - $initialInvestment;
            $decision = $this->makeDecision($npv);

            $rows[] = [
                'scenario' => $this->formatScenarioLabel($pct, 'rate'),
                'pct' => $pct,
                'adjusted_rate' => $adjustedRate,  
                'npv' => $npv,
                'decision' => $decision['short'],
                'is_feasible' => $decision['feasible'],
                'is_base' => $pct === 0,
                'status' => $this->resolveStatus($pct, $decision['feasible']),
            ];
        }

        return $rows;
    }

    private function makeDecision(float $npv): array
    {
        if ($npv > 0) {
            return [
                'label' => 'Worthy Investment / Accepted',
                'short' => 'WORTHY',
                'class' => 'feasible',
                'feasible' => true,
            ];
        }

        if ($npv == 0) {
            return [
                'label' => 'Break Even Investment',
                'short' => 'BREAK EVEN',
                'class' => 'breakeven',
                'feasible' => true,
            ];
        }

        return [
            'label' => 'Bad investment / Declined',
            'short' => 'NOT WORTHY',
            'class' => 'infeasible',
            'feasible' => false,
        ];
    }

    private function formatScenarioLabel(int $pct, string $type): string
    {
        if ($pct === 0)
            return '0% (Base Case)';

        $sign = $pct > 0 ? '+' : '';
        $suffix = match ($type) {
            'revenue' => $pct > 0 ? ' (Income Rises)' : ' (Revenue Down)',
            'rate' => $pct > 0 ? ' (Interest Rates Rise)' : ' (Interest Rates Fall)',
            default => '',
        };

        return abs($pct) === 10
            ? "{$sign}{$pct}%{$suffix}"
            : "{$sign}{$pct}%";
    }

    /**
     *
     * Logic:
     *   - Base case (0%) → "Initial"
     *   - Feasible but not base → "Stable"
     *   - Infeasible → "Changed (Turning point)" = decision turn from base
     */
    private function resolveStatus(int $pct, bool $isFeasible): string
    {
        if ($pct === 0)
            return 'Initial';
        if (!$isFeasible)
            return 'Changed (Turning Point)';
        return 'Stable';
    }
}