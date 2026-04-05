<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NpvCalculatorService;
use App\Repositories\NpvRepository;

/**
 * ============================================================
 *  NPV CONTROLLER  (v2 — dengan Database)
 *  Layer  : HTTP / Controller Layer
 *  Alur POST /npv/calculate:
 *    Input → Validasi → Service (hitung) → Repository (simpan) → View
 * ============================================================
 */
class NpvController extends Controller
{
    public function __construct(
        private readonly NpvCalculatorService $npvService,
        private readonly NpvRepository        $npvRepository
    ) {}

    /** GET /npv — Form input */
    public function index()
    {
        return view('npv.index');
    }

    /** POST /npv/calculate — Validasi → Hitung → Simpan → Redirect */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'project_name'       => 'required|string|max:100',
            'initial_investment' => 'required|numeric|min:0',
            'discount_rate'      => 'required|numeric|min:0|max:100',
            'cash_flows'         => 'required|array|min:1',
            'cash_flows.*'       => 'required|numeric',
        ], [
            'project_name.required'       => 'Nama proyek wajib diisi.',
            'initial_investment.required' => 'Modal awal wajib diisi.',
            'initial_investment.min'      => 'Modal awal tidak boleh negatif.',
            'discount_rate.required'      => 'Tingkat diskonto wajib diisi.',
            'discount_rate.max'           => 'Tingkat diskonto tidak boleh lebih dari 100%.',
            'cash_flows.required'         => 'Minimal satu tahun arus kas harus diisi.',
            'cash_flows.*.required'       => 'Semua kolom arus kas wajib diisi.',
            'cash_flows.*.numeric'        => 'Arus kas harus berupa angka.',
        ]);

        $result  = $this->npvService->calculate(
            (float) $validated['initial_investment'],
            (float) $validated['discount_rate'],
            array_map('floatval', $validated['cash_flows'])
        );

        // Simpan ke DB via Repository
        $project = $this->npvRepository->saveProject($validated['project_name'], $result);

        return redirect()->route('npv.show', $project->id)
                         ->with('success', 'Perhitungan berhasil disimpan!');
    }

    /** GET /npv/{id} — Tampilkan hasil dari DB */
    public function show(int $id)
    {
        $project = $this->npvRepository->findWithCashFlows($id);
        return view('npv.result', compact('project'));
    }

    /** GET /npv/history — Daftar riwayat proyek */
    public function history()
    {
        $projects = $this->npvRepository->getAllProjectsPaginated(10);
        $stats    = $this->npvRepository->getStats();
        return view('npv.history', compact('projects', 'stats'));
    }

    /** DELETE /npv/{id} — Hapus proyek */
    public function destroy(int $id)
    {
        $this->npvRepository->delete($id);
        return redirect()->route('npv.history')
                         ->with('success', 'Proyek berhasil dihapus.');
    }
}