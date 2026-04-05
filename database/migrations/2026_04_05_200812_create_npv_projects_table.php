<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 *  MIGRATION: npv_projects
 *  Tabel utama menyimpan header proyek + hasil perhitungan NPV.
 * ============================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npv_projects', function (Blueprint $table) {
            $table->id();

            // ── Input dari user ──────────────────────────────────────
            $table->string('project_name');               // Nama proyek
            $table->decimal('initial_investment', 20, 2); // Modal awal (C₀)
            $table->decimal('discount_rate', 8, 4);       // Tingkat diskonto (%)

            // ── Hasil perhitungan (disimpan agar tidak re-hitung) ────
            $table->decimal('total_present_value', 20, 2); // Σ PV
            $table->decimal('npv', 20, 2);                 // Nilai NPV final
            $table->string('decision');                    // Label keputusan
            $table->string('decision_class');              // feasible | infeasible | breakeven
            $table->boolean('is_feasible');                // true / false

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npv_projects');
    }
};