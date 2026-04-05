{{-- ============================================================
     VIEW: npv/result.blade.php
     Layer  : Presentation / Frontend Layer
     Tugas  : Menampilkan laporan hasil perhitungan NPV.
              View hanya MENAMPILKAN data yang sudah disiapkan
              oleh Controller & Service — tidak ada logika hitung.
     ============================================================ --}}

@extends('layouts.app')

@section('title', 'Hasil Perhitungan NPV')

@push('styles')
<style>
    /* ── DECISION BANNER ── */
    .decision-banner {
        border-radius: var(--radius-lg);
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }

    .decision-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: .07;
        border-radius: inherit;
    }

    .decision-banner.feasible {
        background: rgba(16,185,129,.08);
        border: 1.5px solid rgba(16,185,129,.3);
    }

    .decision-banner.feasible::before {
        background: radial-gradient(circle at 80% 50%, #10b981, transparent 60%);
        opacity: .15;
    }

    .decision-banner.infeasible {
        background: rgba(244,63,94,.08);
        border: 1.5px solid rgba(244,63,94,.3);
    }

    .decision-banner.infeasible::before {
        background: radial-gradient(circle at 80% 50%, #f43f5e, transparent 60%);
        opacity: .15;
    }

    .decision-banner.breakeven {
        background: rgba(245,158,11,.08);
        border: 1.5px solid rgba(245,158,11,.3);
    }

    .decision-left { flex: 1; min-width: 200px; }

    .decision-meta {
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: .5rem;
    }

    .decision-banner.feasible   .decision-meta { color: var(--clr-success); }
    .decision-banner.infeasible .decision-meta { color: var(--clr-danger); }
    .decision-banner.breakeven  .decision-meta { color: var(--clr-warning); }

    .decision-title {
        font-family: var(--font-display);
        font-size: clamp(1.3rem, 3vw, 2rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
    }

    .decision-project {
        font-size: .9rem;
        color: var(--clr-muted);
        margin-top: .5rem;
    }

    .decision-project strong { color: var(--clr-text); }

    /* ── NPV BADGE ── */
    .npv-badge {
        text-align: right;
    }

    .npv-badge .label {
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--clr-muted);
        margin-bottom: .25rem;
    }

    .npv-value {
        font-family: var(--font-display);
        font-size: clamp(1.5rem, 3vw, 2.2rem);
        font-weight: 800;
    }

    .npv-value.positive { color: var(--clr-success); }
    .npv-value.negative { color: var(--clr-danger); }
    .npv-value.zero     { color: var(--clr-warning); }

    /* ── SUMMARY CARDS ── */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 640px) {
        .summary-grid { grid-template-columns: 1fr; }
    }

    .summary-card {
        background: var(--clr-surface);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
    }

    .summary-card .s-label {
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--clr-muted);
        margin-bottom: .5rem;
    }

    .summary-card .s-value {
        font-family: var(--font-display);
        font-size: 1.35rem;
        font-weight: 700;
        color: #fff;
    }

    .summary-card .s-value.accent { color: var(--clr-accent); }

    /* ── TABLE ── */
    .table-card {
        background: var(--clr-surface);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid var(--clr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-header h2 {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
    }

    .table-header span {
        font-size: .75rem;
        color: var(--clr-muted);
    }

    .table-wrap { overflow-x: auto; }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        background: var(--clr-surface-2);
        padding: .75rem 1.25rem;
        text-align: right;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--clr-muted);
        white-space: nowrap;
    }

    thead th:first-child { text-align: center; }

    tbody tr {
        border-top: 1px solid var(--clr-border);
        transition: background .15s;
    }

    tbody tr:hover { background: rgba(255,255,255,.02); }

    tbody td {
        padding: .85rem 1.25rem;
        text-align: right;
        font-size: .9rem;
        color: var(--clr-text);
        white-space: nowrap;
    }

    tbody td:first-child {
        text-align: center;
        font-family: var(--font-display);
        font-weight: 700;
        color: var(--clr-accent);
    }

    tfoot td {
        padding: 1rem 1.25rem;
        font-weight: 700;
        text-align: right;
        border-top: 2px solid var(--clr-border);
        font-size: .9rem;
        background: var(--clr-surface-2);
    }

    tfoot td:first-child { text-align: center; color: var(--clr-muted); font-size: .8rem; }

    .pv-positive { color: var(--clr-success); }
    .pv-negative { color: var(--clr-danger); }

    /* ── FORMULA DETAIL BOX ── */
    .detail-box {
        background: rgba(14,165,233,.04);
        border: 1px solid rgba(14,165,233,.12);
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
    }

    .detail-box h3 {
        font-family: var(--font-display);
        font-size: .8rem;
        font-weight: 700;
        color: var(--clr-accent-2);
        letter-spacing: .1em;
        text-transform: uppercase;
        margin-bottom: .75rem;
    }

    .detail-step {
        font-family: 'Courier New', monospace;
        font-size: .85rem;
        color: #bae6fd;
        line-height: 2;
    }

    .detail-step .hl { color: var(--clr-accent); }

    /* ── BACK BUTTON ── */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: var(--clr-surface);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-sm);
        color: var(--clr-text);
        padding: .65rem 1.25rem;
        text-decoration: none;
        font-size: .875rem;
        font-weight: 500;
        transition: border-color .2s, color .2s;
    }

    .btn-back:hover {
        border-color: var(--clr-accent);
        color: var(--clr-accent);
    }
</style>
@endpush

@section('content')

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <span class="badge">Laporan Hasil Perhitungan</span>
        <h1>Analisis <span>NPV</span></h1>
        <p>Rincian perhitungan Net Present Value untuk proyek investasi Anda.</p>
    </div>

    {{-- ── DECISION BANNER ── --}}
    <div class="decision-banner {{ $result['decision_class'] }}">
        <div class="decision-left">
            <p class="decision-meta">
                @if($result['is_feasible']) ✔ Keputusan @else ✘ Keputusan @endif
            </p>
            <h2 class="decision-title">{{ $result['decision'] }}</h2>
            <p class="decision-project">
                Proyek: <strong>{{ $projectName }}</strong>
            </p>
        </div>
        <div class="npv-badge">
            <p class="label">Nilai NPV</p>
            <div class="npv-value {{ $result['npv'] > 0 ? 'positive' : ($result['npv'] < 0 ? 'negative' : 'zero') }}">
                Rp {{ number_format($result['npv'], 2, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="summary-grid">
        <div class="summary-card">
            <p class="s-label">Modal Awal (C₀)</p>
            <p class="s-value">Rp {{ number_format($result['initial_investment'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <p class="s-label">Tingkat Diskonto (r)</p>
            <p class="s-value accent">{{ number_format($result['discount_rate'], 2, ',', '.') }}%</p>
        </div>
        <div class="summary-card">
            <p class="s-label">Total PV Arus Kas</p>
            <p class="s-value">Rp {{ number_format($result['total_present_value'], 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- ── DETAIL TABLE ── --}}
    <div class="table-card">
        <div class="table-header">
            <h2>📊 Rincian Present Value per Tahun</h2>
            <span>{{ count($result['yearly_details']) }} periode</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Periode (t)</th>
                        <th>Arus Kas (CFₜ)</th>
                        <th>Faktor Diskonto (1+r)ᵗ</th>
                        <th>Present Value (PV)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result['yearly_details'] as $row)
                    <tr>
                        <td>Tahun {{ $row['year'] }}</td>
                        <td>Rp {{ number_format($row['cash_flow'], 2, ',', '.') }}</td>
                        <td>{{ number_format($row['discount_factor'], 6, ',', '.') }}</td>
                        <td class="{{ $row['present_value'] >= 0 ? 'pv-positive' : 'pv-negative' }}">
                            Rp {{ number_format($row['present_value'], 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td></td>
                        <td></td>
                        <td>Rp {{ number_format($result['total_present_value'], 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── CALCULATION DETAIL ── --}}
    <div class="detail-box">
        <h3>🧮 Langkah Perhitungan NPV</h3>
        <div class="detail-step">
            NPV = <span class="hl">Total PV</span> − <span class="hl">Investasi Awal</span><br>
            NPV = Rp {{ number_format($result['total_present_value'], 2, ',', '.') }}
                  − Rp {{ number_format($result['initial_investment'], 2, ',', '.') }}<br>
            NPV = <strong>Rp {{ number_format($result['npv'], 2, ',', '.') }}</strong>
            &nbsp;→&nbsp; <span class="hl">{{ $result['npv'] > 0 ? 'NPV > 0' : ($result['npv'] < 0 ? 'NPV < 0' : 'NPV = 0') }}</span>
            &nbsp;⇒&nbsp; {{ $result['decision'] }}
        </div>
    </div>

    {{-- ── BACK BUTTON ── --}}
    <div style="display: flex; justify-content: flex-start;">
        <a href="{{ route('npv.index') }}" class="btn-back">
            ← Hitung Proyek Lain
        </a>
    </div>

@endsection
