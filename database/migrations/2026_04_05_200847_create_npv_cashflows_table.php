<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 *  MIGRATION: npv_cash_flows
 *  Tabel detail menyimpan arus kas & PV per tahun per proyek.
 *  Relasi: npv_cash_flows → npv_projects (one-to-many)
 * ============================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npv_cash_flows', function (Blueprint $table) {
            $table->id();

            // Foreign key ke tabel proyek (CASCADE delete)
            $table->foreignId('npv_project_id')
                  ->constrained('npv_projects')
                  ->onDelete('cascade');

            // ── Data per periode ──────────────────────────────────────
            $table->unsignedSmallInteger('year');          // Tahun ke-t (1, 2, 3, ...)
            $table->decimal('cash_flow', 20, 2);           // CFₜ (arus kas)
            $table->decimal('discount_factor', 15, 8);     // (1+r)^t
            $table->decimal('present_value', 20, 2);       // PV = CF / (1+r)^t

            $table->timestamps();

            // Index untuk query ORDER BY year
            $table->index(['npv_project_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npv_cash_flows');
    }
};