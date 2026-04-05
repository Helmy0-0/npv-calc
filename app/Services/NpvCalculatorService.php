<?php

namespace App\Services;

/**
 * ============================================================
 *  NPV CALCULATOR SERVICE
 *  Layer : Business Logic / Service Layer
 *  Tugas : Menjalankan semua perhitungan matematis NPV
 *          TERPISAH dari Controller dan View.
 * ============================================================
 */
class NpvCalculatorService
{
    /**
     * Hitung Present Value (PV) untuk satu periode.
     *
     * Rumus: PV = CF / (1 + r)^t
     *
     * @param  float  $cashFlow   Arus kas pada periode t
     * @param  float  $rate       Tingkat diskonto (desimal, misal 0.10)
     * @param  int    $period     Tahun ke-t
     * @return float
     */
    public function calculatePresentValue(float $cashFlow, float $rate, int $period): float
    {
        // Hindari pembagian nol jika rate = -100%
        if ($rate === -1.0) {
            return 0;
        }

        return $cashFlow / pow(1 + $rate, $period);
    }

    /**
     * Hitung NPV lengkap beserta rincian per tahun.
     *
     * Rumus NPV:
     *   NPV = -Investasi_Awal + Σ [ CF_t / (1 + r)^t ]
     *         untuk t = 1 sampai n
     *
     * @param  float  $initialInvestment  Modal awal (positif)
     * @param  float  $discountRate       Tingkat bunga dalam persen (misal 10)
     * @param  array  $cashFlows          Array arus kas per tahun [CF1, CF2, ...]
     * @return array  Hasil perhitungan lengkap
     */
    public function calculate(float $initialInvestment, float $discountRate, array $cashFlows): array
    {
        // Konversi persen → desimal
        $rate = $discountRate / 100;

        // ── Bangun tabel rincian PV per tahun ──────────────────────────
        $yearlyDetails = [];
        $totalPresentValue = 0;

        foreach ($cashFlows as $index => $cashFlow) {
            $period = $index + 1; // Tahun ke-1, ke-2, dst.

            $discountFactor = pow(1 + $rate, $period);           // (1+r)^t
            $presentValue   = $cashFlow / $discountFactor;        // PV = CF / (1+r)^t

            $totalPresentValue += $presentValue;

            $yearlyDetails[] = [
                'year'            => $period,
                'cash_flow'       => $cashFlow,
                'discount_factor' => $discountFactor,
                'present_value'   => $presentValue,
            ];
        }

        // ── Hitung NPV Final ────────────────────────────────────────────
        // NPV = Total PV semua arus kas masuk  -  Investasi Awal (Tahun 0)
        $npv = $totalPresentValue - $initialInvestment;

        // ── Buat Keputusan Otomatis ─────────────────────────────────────
        $decision = $this->makeDecision($npv);

        return [
            'initial_investment' => $initialInvestment,
            'discount_rate'      => $discountRate,
            'rate_decimal'       => $rate,
            'yearly_details'     => $yearlyDetails,
            'total_present_value'=> $totalPresentValue,
            'npv'                => $npv,
            'decision'           => $decision['label'],
            'decision_class'     => $decision['class'],
            'is_feasible'        => $decision['feasible'],
        ];
    }

    /**
     * Buat keputusan investasi berdasarkan nilai NPV.
     *
     * Aturan:
     *   NPV > 0  →  Layak / Diterima
     *   NPV = 0  →  Break Even (Impas)
     *   NPV < 0  →  Tidak Layak / Ditolak
     *
     * @param  float  $npv
     * @return array
     */
    private function makeDecision(float $npv): array
    {
        if ($npv > 0) {
            return [
                'label'    => 'Investasi LAYAK / DITERIMA',
                'class'    => 'feasible',
                'feasible' => true,
            ];
        }

        if ($npv == 0) {
            return [
                'label'    => 'Investasi BREAK EVEN (Impas)',
                'class'    => 'breakeven',
                'feasible' => true,
            ];
        }

        return [
            'label'    => 'Investasi TIDAK LAYAK / DITOLAK',
            'class'    => 'infeasible',
            'feasible' => false,
        ];
    }
}
