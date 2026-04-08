@extends('layouts.app')

@section('title', 'Input Data Proyek')

@push('styles')
<style>
    /* ── FORM LAYOUT ── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    .form-grid .span-2 { grid-column: span 2; }

    @media (max-width: 640px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-grid .span-2 { grid-column: span 1; }
    }

    /* ── FORM ELEMENTS ── */
    .form-group { display: flex; flex-direction: column; gap: .5rem; }

    label {
        font-size: .8rem;
        font-weight: 500;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--clr-muted);
    }

    label .required { color: var(--clr-danger); margin-left: 3px; }

    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrap .prefix,
    .input-wrap .suffix {
        position: absolute;
        font-size: .85rem;
        font-weight: 600;
        color: var(--clr-accent);
        pointer-events: none;
    }

    .input-wrap .prefix { left: .875rem; }
    .input-wrap .suffix { right: .875rem; }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        background: var(--clr-surface-2);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-sm);
        padding: .7rem 1rem;
        color: var(--clr-text);
        font-family: var(--font-body);
        font-size: .95rem;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
        -moz-appearance: textfield;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; }

    input[type="text"]:focus,
    input[type="number"]:focus {
        border-color: var(--clr-accent);
        box-shadow: 0 0 0 3px rgba(0,212,170,.12);
    }

    .has-prefix input { padding-left: 2.5rem; }
    .has-suffix input { padding-right: 2.75rem; }

    /* ── SECTION DIVIDER ── */
    .section-label {
        font-family: var(--font-display);
        font-size: .95rem;
        font-weight: 700;
        color: #fff;
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--clr-border);
    }

    /* ── CASHFLOW TABLE ── */
    #cashflow-container {
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    .cashflow-row {
        display: grid;
        grid-template-columns: 80px 1fr 40px;
        align-items: center;
        gap: .75rem;
        animation: fadeInRow .25s ease forwards;
    }

    @keyframes fadeInRow {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .year-badge {
        background: rgba(0,212,170,.08);
        border: 1px solid rgba(0,212,170,.2);
        border-radius: var(--radius-sm);
        text-align: center;
        padding: .7rem .5rem;
        font-size: .8rem;
        font-weight: 700;
        color: var(--clr-accent);
        font-family: var(--font-display);
        white-space: nowrap;
    }

    .btn-remove {
        background: rgba(244,63,94,.08);
        border: 1px solid rgba(244,63,94,.2);
        border-radius: var(--radius-sm);
        color: var(--clr-danger);
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1rem;
        transition: background .2s;
        flex-shrink: 0;
    }

    .btn-remove:hover { background: rgba(244,63,94,.18); }

    /* ── BUTTONS ── */
    .btn-add-year {
        background: transparent;
        border: 1px dashed var(--clr-border);
        border-radius: var(--radius-sm);
        color: var(--clr-muted);
        padding: .65rem 1rem;
        font-family: var(--font-body);
        font-size: .875rem;
        cursor: pointer;
        transition: border-color .2s, color .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        margin-top: .25rem;
    }

    .btn-add-year:hover {
        border-color: var(--clr-accent);
        color: var(--clr-accent);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--clr-accent), #00b896);
        border: none;
        border-radius: var(--radius-sm);
        color: #0a0f1e;
        padding: .875rem 2.5rem;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform .15s, box-shadow .2s;
        letter-spacing: .04em;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(0,212,170,.3);
    }

    .btn-submit:active { transform: translateY(0); }

    /* ── INFO BOX ── */
    .formula-box {
        background: rgba(14,165,233,.05);
        border: 1px solid rgba(14,165,233,.15);
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
    }

    .formula-box h3 {
        font-family: var(--font-display);
        font-size: .85rem;
        font-weight: 700;
        color: var(--clr-accent-2);
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: .75rem;
    }

    .formula-code {
        font-family: 'Courier New', monospace;
        font-size: .9rem;
        color: #bae6fd;
        line-height: 1.8;
    }

    .formula-code .comment {
        color: var(--clr-muted);
        font-style: italic;
    }
</style>
@endpush

@section('content')

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <span class="badge">Decision Support System</span>
        <h1><span>NPV</span>Investment Calculator</h1>
        <p>Enter project data to calculate investment feasibility using the Net Present Value method.</p>
    </div>

    {{-- ── FORMULA INFO BOX ── --}}
    <div class="formula-box">
        <h3>NPV Formula</h3>
        <div class="formula-code">
            NPV = -C₀ + Σ [ CFₜ / (1 + r)ᵗ ]<br>
            <span class="comment">
                &nbsp;&nbsp;C₀ = Initial Investment, CFₜ = Cash Flow Year t, r = Discount Rate
            </span>
        </div>
    </div>

    {{-- ── MAIN FORM CARD ── --}}
    <div class="card">
        <form action="{{ route('npv.calculate') }}" method="POST" id="npv-form">
            @csrf

            {{--PROJECT INFO--}}
            <p class="section-label">① Project Information</p>

            <div class="form-grid" style="margin-bottom: 2rem;">

                {{-- Project Name --}}
                <div class="form-group span-2">
                    <label>Project Name<span class="required">*</span></label>
                    <input
                        type="text"
                        name="project_name"
                        placeholder="example: Construction of Unit B Factory"
                        value="{{ old('project_name') }}"
                        autocomplete="off"
                        required
                    >
                </div>

                {{-- Modal Awal --}}
                <div class="form-group">
                    <label>Initial capital / Investment (Rp) <span class="required">*</span></label>
                    <div class="input-wrap has-prefix">
                        <span class="prefix">Rp</span>
                        <input
                            type="number"
                            name="initial_investment"
                            placeholder="500,000,000"
                            value="{{ old('initial_investment') }}"
                            min="0"
                            step="any"
                            required
                        >
                    </div>
                </div>

                {{-- Discount Rate --}}
                <div class="form-group">
                    <label>Discount Rate <span class="required">*</span></label>
                    <div class="input-wrap has-suffix">
                        <input
                            type="number"
                            name="discount_rate"
                            placeholder="10"
                            value="{{ old('discount_rate') }}"
                            min="0"
                            max="100"
                            step="any"
                            required
                        >
                        <span class="suffix">%</span>
                    </div>
                </div>

            </div>

            {{--CASHFLOW --}}
            <p class="section-label">② Cash Inflow per Year</p>

            <div id="cashflow-container">
                <div class="cashflow-row" data-index="0">
                    <span class="year-badge">Year 1</span>
                    <div class="input-wrap has-prefix">
                        <span class="prefix">Rp</span>
                        <input
                            type="number"
                            name="cash_flows[]"
                            placeholder="Cash flow for year-1"
                            value="{{ old('cash_flows.0') }}"
                            step="any"
                            required
                        >
                    </div>
                    <div></div>
                </div>
            </div>

            <button type="button" class="btn-add-year" id="btn-add-year">
                <span>＋</span> Add Year
            </button>

            {{--SUBMIT--}}
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-submit">
                    Calculate NPV →
                </button>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
<script>
    /**
     *  FRONTEND JAVASCRIPT
     */
    (function () {
        const container = document.getElementById('cashflow-container');
        const btnAdd    = document.getElementById('btn-add-year');

        function getRowCount() {
            return container.querySelectorAll('.cashflow-row').length;
        }

        function createRow(yearNumber) {
            const row = document.createElement('div');
            row.className = 'cashflow-row';
            row.dataset.index = yearNumber - 1;

            row.innerHTML = `
                <span class="year-badge">Year ${yearNumber}</span>
                <div class="input-wrap has-prefix">
                    <span class="prefix">Rp</span>
                    <input
                        type="number"
                        name="cash_flows[]"
                        placeholder="Cash flow for year-${yearNumber}"
                        step="any"
                        required
                    >
                </div>
                <button type="button" class="btn-remove" title="Hapus tahun ini">×</button>
            `;

            row.querySelector('.btn-remove').addEventListener('click', function () {
                row.remove();
                reindexRows();
            });

            return row;
        }
        function reindexRows() {
            const rows = container.querySelectorAll('.cashflow-row');
            rows.forEach(function (row, i) {
                const yearNum  = i + 1;
                const badge    = row.querySelector('.year-badge');
                const input    = row.querySelector('input');

                badge.textContent        = `Year ${yearNum}`;
                input.placeholder        = `Cash flow for year-${yearNum}`;
                row.dataset.index        = i;
            });
        }

        btnAdd.addEventListener('click', function () {
            const newYear = getRowCount() + 1;
            const newRow  = createRow(newYear);
            container.appendChild(newRow);
            newRow.querySelector('input').focus();
        });

        document.getElementById('npv-form').addEventListener('submit', function (e) {
            if (getRowCount() < 1) {
                e.preventDefault();
                alert('At least one cash flow row must be filled in.');
            }
        });

    })();
</script>
@endpush