@extends('layouts.app')

@section('title', 'History NPV Projects')

@push('styles')
<style>
    /* ── STATS CARDS ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    @media(max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--clr-surface);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        border-radius: 3px 0 0 3px;
    }
    .stat-card.total::before    { background: var(--clr-accent); }
    .stat-card.feasible::before { background: var(--clr-success); }
    .stat-card.infeasible::before { background: var(--clr-danger); }
    .stat-card.avg::before      { background: var(--clr-accent-2); }

    .stat-label { font-size:.7rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:var(--clr-muted); margin-bottom:.5rem; }
    .stat-value { font-family:var(--font-display); font-size:1.6rem; font-weight:800; color:#fff; }
    .stat-value.small { font-size:1.1rem; }
    .stat-value.success { color:var(--clr-success); }
    .stat-value.danger  { color:var(--clr-danger); }
    .stat-value.info    { color:var(--clr-accent-2); }

    /* ── TABLE CARD ── */
    .history-table-card {
        background: var(--clr-surface);
        border: 1px solid var(--clr-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    .table-toolbar {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid var(--clr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .table-toolbar h2 { font-family:var(--font-display); font-size:1rem; font-weight:700; color:#fff; }

    .btn-new {
        background: linear-gradient(135deg, var(--clr-accent), #00b896);
        border: none;
        border-radius: var(--radius-sm);
        color: #0a0f1e;
        padding: .55rem 1.25rem;
        font-family: var(--font-display);
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: transform .15s, box-shadow .2s;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }
    .btn-new:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(0,212,170,.25); }

    /* ── TABLE ── */
    .table-wrap { overflow-x: auto; }
    table { width:100%; border-collapse:collapse; }
    thead th {
        background:var(--clr-surface-2); padding:.75rem 1.25rem;
        text-align:left; font-size:.72rem; font-weight:700;
        letter-spacing:.1em; text-transform:uppercase; color:var(--clr-muted);
        white-space:nowrap;
    }
    thead th.num { text-align:right; }
    tbody tr { border-top:1px solid var(--clr-border); transition:background .15s; }
    tbody tr:hover { background:rgba(255,255,255,.025); }
    tbody td { padding:.9rem 1.25rem; font-size:.875rem; color:var(--clr-text); white-space:nowrap; }
    tbody td.num { text-align:right; }

    /* ── STATUS BADGE ── */
    .badge-feasible {
        display:inline-block; padding:.25rem .7rem;
        border-radius:100px; font-size:.7rem; font-weight:700;
        background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25);
        color:var(--clr-success);
    }
    .badge-infeasible {
        display:inline-block; padding:.25rem .7rem;
        border-radius:100px; font-size:.7rem; font-weight:700;
        background:rgba(244,63,94,.1); border:1px solid rgba(244,63,94,.25);
        color:var(--clr-danger);
    }
    .badge-breakeven {
        display:inline-block; padding:.25rem .7rem;
        border-radius:100px; font-size:.7rem; font-weight:700;
        background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.25);
        color:var(--clr-warning);
    }

    /* ── ROW ACTIONS ── */
    .row-actions { display:flex; gap:.5rem; align-items:center; }
    .btn-detail {
        font-size:.78rem; font-weight:600;
        color:var(--clr-accent-2); text-decoration:none;
        border:1px solid rgba(14,165,233,.25); border-radius:var(--radius-sm);
        padding:.3rem .75rem; transition:background .2s;
    }
    .btn-detail:hover { background:rgba(14,165,233,.08); }
    .btn-del {
        font-size:.78rem; font-weight:600; cursor:pointer;
        color:var(--clr-danger); background:none;
        border:1px solid rgba(244,63,94,.2); border-radius:var(--radius-sm);
        padding:.3rem .75rem; transition:background .2s;
    }
    .btn-del:hover { background:rgba(244,63,94,.1); }

    /* ── EMPTY STATE ── */
    .empty-state {
        text-align:center; padding:4rem 2rem;
        color:var(--clr-muted);
    }
    .empty-state .icon { font-size:3rem; margin-bottom:1rem; opacity:.4; }
    .empty-state p { font-size:.95rem; }

    /* ── PAGINATION ── */
    .pagination-wrap {
        padding:1rem 1.75rem;
        border-top:1px solid var(--clr-border);
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        flex-wrap:wrap;
    }
    .pagination-wrap .info { font-size:.8rem; color:var(--clr-muted); }
    .pagination { display:flex; gap:.4rem; list-style:none; }
    .pagination li span,
    .pagination li a {
        display:inline-flex; align-items:center; justify-content:center;
        width:34px; height:34px;
        border-radius:var(--radius-sm);
        font-size:.8rem; text-decoration:none;
        border:1px solid var(--clr-border);
        color:var(--clr-muted);
        transition:background .15s,color .15s,border-color .15s;
    }
    .pagination li a:hover { border-color:var(--clr-accent); color:var(--clr-accent); }
    .pagination li.active span {
        background:var(--clr-accent); border-color:var(--clr-accent);
        color:#0a0f1e; font-weight:700;
    }
    .pagination li.disabled span { opacity:.3; cursor:default; }
</style>
@endpush

@section('content')

    @if(session('success'))
        <div style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.25);border-radius:var(--radius-md);padding:.85rem 1.25rem;margin-bottom:1.5rem;color:#6ee7b7;font-size:.875rem;">
            success {{ session('success') }}
        </div>
    @endif

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <span class="badge">Database</span>
        <h1>Project <span>History</span></h1>
    </div>

    {{-- ── STATS CARDS ── --}}
    <div class="stats-grid">
        <div class="stat-card total">
            <p class="stat-label">Total Project</p>
            <p class="stat-value">{{ $stats['total'] }}</p>
        </div>
        <div class="stat-card feasible">
            <p class="stat-label">Worthy</p>
            <p class="stat-value success">{{ $stats['feasible'] }}</p>
        </div>
        <div class="stat-card infeasible">
            <p class="stat-label">Bad</p>
            <p class="stat-value danger">{{ $stats['infeasible'] }}</p>
        </div>
        <div class="stat-card avg">
            <p class="stat-label">NPV Average</p>
            <p class="stat-value info small">Rp {{ number_format($stats['avg_npv'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- ── PROJECT TABLE ── --}}
    <div class="history-table-card">
        <div class="table-toolbar">
            <h2>Project List</h2>
            <a href="{{ route('npv.index') }}" class="btn-new">＋ New Project</a>
        </div>

        @if($projects->isEmpty())
            <div class="empty-state">
                <p>There are no projects saved yet.<br>
                   <a href="{{ route('npv.index') }}" style="color:var(--clr-accent)">Calculate your first project →</a>
                </p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project Name</th>
                            <th class="num">Initial capital</th>
                            <th class="num">Discount Rate</th>
                            <th class="num">Year</th>
                            <th class="num">NPV</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td style="color:var(--clr-muted);font-size:.8rem;">{{ $project->id }}</td>
                            <td>
                                <a href="{{ route('npv.show', $project->id) }}"
                                   style="color:#fff;text-decoration:none;font-weight:500;hover:color:var(--clr-accent)">
                                    {{ $project->project_name }}
                                </a>
                            </td>
                            <td class="num">Rp {{ number_format($project->initial_investment, 0, ',', '.') }}</td>
                            <td class="num">{{ number_format($project->discount_rate, 2, ',', '.') }}%</td>
                            <td class="num">{{ $project->cash_flows_count }} thn</td>
                            <td class="num" style="font-weight:600;color:{{ $project->npv > 0 ? 'var(--clr-success)' : ($project->npv < 0 ? 'var(--clr-danger)' : 'var(--clr-warning)') }}">
                                Rp {{ number_format($project->npv, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge-{{ $project->decision_class }}">
                                    {{ $project->is_feasible ? 'Worth' : ($project->decision_class === 'breakeven' ? 'Break Even' : 'Bad') }}
                                </span>
                            </td>
                            <td style="color:var(--clr-muted);font-size:.8rem;">
                                {{ $project->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('npv.show', $project->id) }}" class="btn-detail">Detail</a>
                                    <form action="{{ route('npv.destroy', $project->id) }}" method="POST"
                                          onsubmit="return confirm('Delete Project {{ addslashes($project->project_name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ── PAGINATION ── --}}
            @if($projects->hasPages())
            <div class="pagination-wrap">
                <span class="info">
                    Displaying {{ $projects->firstItem() }}–{{ $projects->lastItem() }}
                    from {{ $projects->total() }} project
                </span>
                <ul class="pagination">
                    {{-- Previous --}}
                    <li class="{{ $projects->onFirstPage() ? 'disabled' : '' }}">
                        @if($projects->onFirstPage())
                            <span>‹</span>
                        @else
                            <a href="{{ $projects->previousPageUrl() }}">‹</a>
                        @endif
                    </li>

                    {{-- Page numbers --}}
                    @foreach($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                        <li class="{{ $page == $projects->currentPage() ? 'active' : '' }}">
                            @if($page == $projects->currentPage())
                                <span>{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach

                    {{-- Next --}}
                    <li class="{{ !$projects->hasMorePages() ? 'disabled' : '' }}">
                        @if($projects->hasMorePages())
                            <a href="{{ $projects->nextPageUrl() }}">›</a>
                        @else
                            <span>›</span>
                        @endif
                    </li>
                </ul>
            </div>
            @endif

        @endif
    </div>

@endsection