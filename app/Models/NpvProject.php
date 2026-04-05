<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ============================================================
 *  MODEL: NpvProject
 *  Layer  : Data / Model Layer
 *  Tugas  : Merepresentasikan tabel npv_projects.
 *           Mendefinisikan relasi ke NpvCashFlow.
 * ============================================================
 *
 * @property int         $id
 * @property string      $project_name
 * @property float       $initial_investment
 * @property float       $discount_rate
 * @property float       $total_present_value
 * @property float       $npv
 * @property string      $decision
 * @property string      $decision_class
 * @property bool        $is_feasible
 * @property \Carbon\Carbon $created_at
 */
class NpvProject extends Model
{
    use HasFactory;

    protected $table = 'npv_projects';

    /**
     * Kolom yang boleh di-mass assign (via create/fill).
     */
    protected $fillable = [
        'project_name',
        'initial_investment',
        'discount_rate',
        'total_present_value',
        'npv',
        'decision',
        'decision_class',
        'is_feasible',
    ];

    /**
     * Cast otomatis tipe data kolom.
     */
    protected $casts = [
        'initial_investment'  => 'float',
        'discount_rate'       => 'float',
        'total_present_value' => 'float',
        'npv'                 => 'float',
        'is_feasible'         => 'boolean',
    ];

    // ── RELASI ────────────────────────────────────────────────────────

    /**
     * Satu proyek memiliki banyak baris arus kas.
     * Eager load otomatis diurutkan berdasarkan tahun.
     */
    public function cashFlows(): HasMany
    {
        return $this->hasMany(NpvCashFlow::class, 'npv_project_id')
                    ->orderBy('year');
    }
}