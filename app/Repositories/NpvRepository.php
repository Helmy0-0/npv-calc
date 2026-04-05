<?php

namespace App\Repositories;

use App\Models\NpvProject;
use App\Models\NpvCashFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

/**
 * ============================================================
 *  NPV REPOSITORY
 *  Layer  : Data Access / Repository Layer
 *  Tugas  : Semua operasi database untuk NPV.
 *           Controller & Service TIDAK boleh query Eloquent langsung.
 *           Semua akses DB lewat sini.
 * ============================================================
 */
class NpvRepository
{
    /**
     * Simpan proyek baru + semua detail arus kasnya ke database.
     * Dibungkus dalam DB Transaction agar atomik:
     * jika salah satu insert gagal, semua di-rollback.
     *
     * @param  string  $projectName
     * @param  array   $result  Output dari NpvCalculatorService::calculate()
     * @return NpvProject  Model yang baru disimpan (dengan id)
     */
    public function saveProject(string $projectName, array $result): NpvProject
    {
        return DB::transaction(function () use ($projectName, $result) {

            // ── 1. Simpan header proyek ──────────────────────────────
            $project = NpvProject::create([
                'project_name'        => $projectName,
                'initial_investment'  => $result['initial_investment'],
                'discount_rate'       => $result['discount_rate'],
                'total_present_value' => $result['total_present_value'],
                'npv'                 => $result['npv'],
                'decision'            => $result['decision'],
                'decision_class'      => $result['decision_class'],
                'is_feasible'         => $result['is_feasible'],
            ]);

            // ── 2. Simpan detail arus kas per tahun (bulk insert) ────
            $cashFlowRows = array_map(fn($row) => [
                'npv_project_id' => $project->id,
                'year'           => $row['year'],
                'cash_flow'      => $row['cash_flow'],
                'discount_factor'=> $row['discount_factor'],
                'present_value'  => $row['present_value'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ], $result['yearly_details']);

            NpvCashFlow::insert($cashFlowRows); // Lebih efisien dari loop create()

            return $project;
        });
    }

    /**
     * Ambil semua proyek untuk halaman riwayat.
     * Diurutkan dari yang terbaru, dengan count cashFlows.
     *
     * @param  int  $perPage  Jumlah item per halaman (pagination)
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllProjectsPaginated(int $perPage = 10)
    {
        return NpvProject::withCount('cashFlows')   // Tambahkan kolom cash_flows_count
                         ->orderByDesc('created_at')
                         ->paginate($perPage);
    }

    /**
     * Ambil satu proyek beserta semua detail arus kasnya.
     * Menggunakan Eager Loading untuk menghindari N+1 query.
     *
     * @param  int  $id
     * @return NpvProject
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithCashFlows(int $id): NpvProject
    {
        return NpvProject::with('cashFlows')  // Eager load relasi
                         ->findOrFail($id);
    }

    /**
     * Hapus satu proyek (cascade ke cash_flows otomatis via FK).
     *
     * @param  int  $id
     * @return void
     */
    public function delete(int $id): void
    {
        NpvProject::findOrFail($id)->delete();
    }

    /**
     * Statistik ringkas untuk dashboard riwayat.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total'      => NpvProject::count(),
            'feasible'   => NpvProject::where('is_feasible', true)->count(),
            'infeasible' => NpvProject::where('is_feasible', false)->count(),
            'avg_npv'    => NpvProject::avg('npv') ?? 0,
        ];
    }
}