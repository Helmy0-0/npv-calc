<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NpvCalculatorService;

/**
 * ============================================================
 *  NPV CONTROLLER
 *  Layer : HTTP / Controller Layer
 *  Tugas : Menerima request dari form, memvalidasi input,
 *          lalu mendelegasikan perhitungan ke Service Layer.
 *          Controller TIDAK mengandung logika matematis.
 * ============================================================
 */
class NpvController extends Controller
{
    /**
     * Inject NpvCalculatorService melalui Constructor Injection.
     * Ini memisahkan tanggung jawab antara HTTP handling & bisnis logic.
     */
    public function __construct(
        private readonly NpvCalculatorService $npvService
    ) {}

    /**
     * GET /npv
     * Tampilkan halaman form input.
     */
    public function index()
    {
        return view('npv.index');
    }

    /**
     * POST /npv/calculate
     * Terima data form, validasi, hitung, kirim hasil ke view.
     */
    public function calculate(Request $request)
    {
        // ── 1. VALIDASI INPUT ───────────────────────────────────────────
        $validated = $request->validate([
            'project_name'       => 'required|string|max:100',
            'initial_investment' => 'required|numeric|min:0',
            'discount_rate'      => 'required|numeric|min:0|max:100',
            'cash_flows'         => 'required|array|min:1',
            'cash_flows.*'       => 'required|numeric',
        ], [
            // Pesan error dalam Bahasa Indonesia
            'project_name.required'       => 'Nama proyek wajib diisi.',
            'initial_investment.required' => 'Modal awal wajib diisi.',
            'initial_investment.min'      => 'Modal awal tidak boleh negatif.',
            'discount_rate.required'      => 'Tingkat diskonto wajib diisi.',
            'discount_rate.max'           => 'Tingkat diskonto tidak boleh lebih dari 100%.',
            'cash_flows.required'         => 'Minimal satu tahun arus kas harus diisi.',
            'cash_flows.*.required'       => 'Semua kolom arus kas wajib diisi.',
            'cash_flows.*.numeric'        => 'Arus kas harus berupa angka.',
        ]);

        // ── 2. SIAPKAN DATA ─────────────────────────────────────────────
        $projectName        = $validated['project_name'];
        $initialInvestment  = (float) $validated['initial_investment'];
        $discountRate       = (float) $validated['discount_rate'];
        $cashFlows          = array_map('floatval', $validated['cash_flows']);

        // ── 3. DELEGASI KE SERVICE LAYER ────────────────────────────────
        // Controller tidak tahu CARA menghitung, hanya tahu SIAPA yang menghitung.
        $result = $this->npvService->calculate(
            $initialInvestment,
            $discountRate,
            $cashFlows
        );

        // ── 4. KIRIM DATA KE VIEW ────────────────────────────────────────
        return view('npv.result', [
            'projectName' => $projectName,
            'result'      => $result,
        ]);
    }
}
