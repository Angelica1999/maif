@extends('layouts.app')

@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <div>
                    <h4 class="card-title mb-1">Track Recomendation/Bugs</h4>
                    <p class="card-description mb-0">MAIF-IPP</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center"
                            type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="me-2">
                                <path d="M2 4H14M4 8H12M6 12H10" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <circle cx="12" cy="4" r="1.5" fill="currentColor" stroke="white" stroke-width="1" />
                                <circle cx="8" cy="8" r="1.5" fill="currentColor" stroke="white" stroke-width="1" />
                                <circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="white" stroke-width="1" />
                            </svg>
                            Filter:
                            <span id="selected-filter" class="ms-1 fw-normal">
                                @if (request('filter') == 'approved') Approved
                                @elseif(request('filter') == 'in_progress') In Progress
                                @elseif(request('filter') == 'done') Done
                                @elseif(request('filter') == 'pending') Pending
                                @elseif(request('filter') == 'rejected') Rejected
                                @elseif(request('filter') == 'recommendations') Recommendations
                                @elseif(request('filter') == 'bugs') Bugs
                                @else All Items
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown" style="min-width: 200px;">
                            <li>
                                <a class="dropdown-item {{ !request('filter') ? 'active' : '' }}" href="{{ route('recommendations.view') }}">
                                    <div class="d-flex align-items-center"><span class="me-2"></span> All Items</div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">By Status</h6></li>
                            <li>
                                <a class="dropdown-item {{ request('filter') == 'pending' ? 'active' : '' }}" href="{{ route('recommendations.view', ['filter' => 'pending']) }}">
                                    <div class="d-flex align-items-center">Pending</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('filter') == 'approved' ? 'active' : '' }}" href="{{ route('recommendations.view', ['filter' => 'approved']) }}">
                                    <div class="d-flex align-items-center">Approved</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('filter') == 'in_progress' ? 'active' : '' }}" href="{{ route('recommendations.view', ['filter' => 'in_progress']) }}">
                                    <div class="d-flex align-items-center">In Progress</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('filter') == 'done' ? 'active' : '' }}" href="{{ route('recommendations.view', ['filter' => 'done']) }}">
                                    <div class="d-flex align-items-center">Done</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('filter') == 'rejected' ? 'active' : '' }}" href="{{ route('recommendations.view', ['filter' => 'rejected']) }}">
                                    <div class="d-flex align-items-center">Rejected</div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">By Type</h6></li>
                            <li>
                                <a class="dropdown-item {{ request('filter') == 'recommendations' ? 'active' : '' }}" href="{{ route('recommendations.view', ['filter' => 'recommendations']) }}">
                                    <div class="d-flex align-items-center"><span class="type-icon type-rec me-2"></span> Recommendations</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('filter') == 'bugs' ? 'active' : '' }}" href="{{ route('recommendations.view', ['filter' => 'bugs']) }}">
                                    <div class="d-flex align-items-center"><span class="type-icon type-bug me-2"></span> Bugs</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            @if (request('filter'))
                <div class="mb-3 d-flex align-items-center">
                    <span class="badge bg-light text-dark py-2 px-3 d-flex align-items-center">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                            <path d="M2 4H14M4 8H12M6 12H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        Active Filter:
                        <strong class="ms-1">
                            @if (request('filter') == 'approved') Approved Items
                            @elseif(request('filter') == 'in_progress') In Progress Items
                            @elseif(request('filter') == 'done') Done Items
                            @elseif(request('filter') == 'pending') Pending Items
                            @elseif(request('filter') == 'rejected') Rejected Items
                            @elseif(request('filter') == 'recommendations') Recommendations Only
                            @elseif(request('filter') == 'bugs') Bugs Only
                            @endif
                        </strong>
                        <a href="{{ route('recommendations.view') }}" class="ms-3 text-danger text-decoration-none" style="font-size: 1.2rem; line-height: 1;">&times;</a>
                    </span>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success py-2 mb-3">&#10003; {{ session('success') }}</div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger py-2 mb-3">&#9888; {{ session('error') }}</div>
            @endif
            
            <div class="mb-3 text-muted small">
                Showing {{ $recommendations->firstItem() ?? 0 }} - {{ $recommendations->lastItem() ?? 0 }}
                of {{ $recommendations->total() }} items
            </div>
            
            @if ($recommendations->isEmpty())
             <div class="alert alert-danger" role="alert" style="width: 100%; margin-top:50px">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No recommendation or bugs found</strong>
                </div>
            @else
                <div class="trk-grid">
                    @foreach ($recommendations as $rec)
                        @php
                            $statusMap = [
                                'approved'    => ['label' => 'Approved',     'css' => 'trk-approved'],
                                'in_progress' => ['label' => 'In Progress',  'css' => 'trk-progress'],
                                'done'        => ['label' => 'Done',         'css' => 'trk-done'],
                                'pending'     => ['label' => 'Pending',      'css' => 'trk-pending'],
                                'rejected'    => ['label' => 'Rejected',     'css' => 'trk-rejected'],
                            ];
                            $s = $statusMap[$rec->status] ?? $statusMap['pending'];
                            $isRec     = !isset($rec->type) || $rec->type !== 'bug';
                            $typeLabel = $isRec ? 'RECOMMENDATION' : 'BUGS';
                            $typeCss   = $isRec ? 'trk-type-rec' : 'trk-type-bug';

                            $dateActioned = $rec->status !== 'pending' ? $rec->updated_at->format('M d, Y') : '';

                            $dateLabelMap = [
                                'approved'    => 'DATE APPROVED',
                                'in_progress' => 'LAST UPDATED',
                                'done'        => 'DATE COMPLETED',
                                'rejected'    => 'DATE REJECTED',
                                'pending'     => 'DATE SUBMITTED',
                            ];
                            $dateLabel = $dateLabelMap[$rec->status] ?? 'LAST UPDATED';
                            $dateValue = $dateActioned ?: ($rec->status === 'pending' ? $rec->created_at->format('M d, Y') : '—');

                            $hasReplies = $rec->replies()->count() > 0;
                            $replyCount = $hasReplies ? $rec->replies()->count() : 0;
                            
                            $showChat = $rec->type === 'bug';
                        @endphp

                        <div class="trk-card"
                            data-id="{{ $rec->id }}"
                            data-status="{{ $rec->status }}"
                            data-status-label="{{ $s['label'] }}"
                            data-status-css="{{ $s['css'] }}"
                            data-type="{{ $typeLabel }}"
                            data-type-css="{{ $typeCss }}"
                            data-date-label="{{ $dateLabel }}"
                            data-date-actioned="{{ $dateValue }}"
                            data-date-submitted="{{ $rec->created_at->format('M d, Y') }}"
                            data-message="{{ e($rec->recommendation) }}"
                            data-remarks="{{ e($rec->remarks ?? '') }}"
                            data-evaluated-by="{{ e($rec->evaluated_by ?? '') }}">

                            {{-- Row 1: Status | Dynamic Date Label --}}
                            <div class="trk-top">
                                <span class="trk-status {{ $s['css'] }}">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <circle cx="7" cy="7" r="6.3" stroke="currentColor" stroke-width="1.4" />
                                        <path d="M4.5 7l2 2 3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ $s['label'] }}
                                    @if($hasReplies)
                                        <span class="reply-indicator" style="margin-left: 5px;">💬</span>
                                    @endif
                                </span>
                                <div class="trk-date-block">
                                    <span class="trk-date-label">{{ $dateLabel }}</span>
                                    <span class="trk-date-val">{{ $dateValue }}</span>
                                </div>
                            </div>
                            
                            <div class="trk-admin-label">ADMIN RESPONDS</div>
                            <div class="trk-admin-box">
                                @if ($rec->remarks)
                                    {{ Str::limit($rec->remarks, 80) }}
                                    @if ($rec->evaluated_by)
                                        <div class="trk-admin-by">— {{ $rec->evaluated_by }}</div>
                                    @endif
                                @else
                                    <span class="trk-awaiting">
                                        @if ($rec->status === 'pending') Awaiting review… @else No remarks added. @endif
                                    </span>
                                @endif
                            </div>
                            
                            <div class="trk-type {{ $typeCss }}">{{ $typeLabel }}</div>
                            
                            <div class="trk-footer">
                                <span class="trk-bottom-date">{{ $rec->created_at->format('M d, Y') }}</span>
                                <div>
                                    @if($showChat)
                                        <a href="{{ route('recommendations.conversation', $rec->id) }}"
                                           class="trk-reply-btn {{ $hasReplies ? 'trk-reply-btn--active' : '' }}">
                                            💬 Chat
                                            @if($hasReplies)
                                                <span class="trk-reply-count">{{ $replyCount }}</span>
                                            @endif
                                        </a>
                                    @endif
                                    <button class="trk-view-btn" onclick="openModal(this)">View</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

              <div class="mt-3" id="pagination_links">
                    {{ $recommendations->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>
</div>

<!-- Modal -->
<div class="trk-modal-overlay" id="trkModal" onclick="closeModalOutside(event)">
    <div class="trk-modal">
        <div class="trk-modal-header">
            <div class="trk-modal-title">
                <span id="modal-type-label" class="trk-modal-type"></span>
                <span id="modal-id" class="trk-modal-id"></span>
            </div>
            <button class="trk-modal-close" onclick="closeModal()">&#10005;</button>
        </div>

        <div class="trk-modal-statusbar">
            <span id="modal-status-badge" class="trk-status"></span>
            <span class="trk-modal-meta-dates">
                <span class="trk-modal-meta-item">
                    <span class="trk-modal-meta-label">SUBMITTED</span>
                    <span id="modal-date-submitted" class="trk-modal-meta-val"></span>
                </span>
                <span class="trk-modal-meta-sep">&#183;</span>
                <span class="trk-modal-meta-item">
                    <span id="modal-date-actioned-label" class="trk-modal-meta-label">DATE APPROVED</span>
                    <span id="modal-date-actioned" class="trk-modal-meta-val"></span>
                </span>
            </span>
        </div>

        <div class="trk-modal-body">
            <div class="trk-modal-section">
                <div class="trk-modal-section-label">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M1 1h12v9H8l-3 3V10H1V1z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" />
                    </svg>
                    YOUR MESSAGE
                </div>
                <div class="trk-modal-message-box" id="modal-message"></div>
            </div>

            <div class="trk-modal-section">
                <div class="trk-modal-section-label">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="6.3" stroke="currentColor" stroke-width="1.4" />
                        <path d="M4.5 7l2 2 3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    ADMIN RESPONDS
                </div>
                <div class="trk-modal-admin-box">
                    <p id="modal-remarks" class="trk-modal-remarks"></p>
                    <p id="modal-evaluated-by" class="trk-modal-eval-by"></p>
                </div>
            </div>
        </div>

        <div class="trk-modal-footer">
            <button class="trk-modal-close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<style>
.trk-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
@media (max-width: 600px) { .trk-grid { grid-template-columns: 1fr; } }

.trk-card {
    background: #fff;
    border: 1.5px solid #d6d6d6;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.trk-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.10); border-color: #bbb; }

.trk-top {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px 8px;
    border-bottom: 1px solid #ebebeb;
}
.trk-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 12px; border-radius: 20px;
    font-size: 0.82rem; font-weight: 700;
}
.trk-approved { background: #e6f4ec; color: #659934; border: 1.5px solid #b5dfc8; }
.trk-pending  { background: #f5eddb; color: #bda30f; border: 1.5px solid #ddc381; }
.trk-rejected { background: #fce3e3; color: #f80e25; border: 1.5px solid #f0b8b8; }
.trk-progress { background: #fcebd4; color: #e08309; border: 1.5px solid #d8b280; }
.trk-done { background: #e6f4ec; color: #046424; border: 1.5px solid #8cb39a; }

.trk-date-block { display: flex; flex-direction: column; align-items: flex-end; }
.trk-date-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #aaa; line-height: 1.2; }
.trk-date-val   { font-size: 0.75rem; color: #666; }

.trk-admin-label { padding: 10px 14px 4px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; color: #333; text-transform: uppercase; }
.trk-admin-box   { margin: 0 14px 10px; background: #e8e8e8; border-radius: 4px; min-height: 72px; padding: 8px 10px; font-size: 0.82rem; color: #444; line-height: 1.5; }
.trk-admin-by    { font-size: 0.72rem; color: #888; margin-top: 4px; }
.trk-awaiting    { font-size: 0.78rem; color: #aaa; font-style: italic; }

.trk-type     { padding: 6px 14px 2px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; }
.trk-type-rec { color: #126ec5; }
.trk-type-bug { color: #aa3a31; }

.trk-footer { display: flex; align-items: center; justify-content: space-between; padding: 6px 14px 10px; }
.trk-bottom-date { font-size: 0.75rem; color: #aaa; }
.trk-view-btn {
    background: #007ba0; color: #fff; border: none;
    border-radius: 5px; padding: 4px 16px;
    font-size: 0.78rem; font-weight: 600; cursor: pointer;
    transition: background 0.15s, transform 0.1s;
}
.trk-view-btn:hover { background: #277992; transform: translateY(-1px); }

.trk-reply-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 4px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    margin-right: 5px;
    transition: background 0.15s, transform 0.1s;
}
.trk-reply-btn:hover {
    background: #3d8b40;
    transform: translateY(-1px);
    color: white;
    text-decoration: none;
}
.trk-reply-btn--active {
    background: #2e7d32;
}
.trk-reply-count {
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
    padding: 0 6px;
    font-size: 0.7rem;
    font-weight: 700;
    min-width: 18px;
    text-align: center;
}

.type-icon { font-size: 1rem; line-height: 1; }
.type-rec  { color: #126ec5; }
.type-bug  { color: #aa3a31; }
.dropdown-item.active { background-color: #f0f0ff; color: #3f3f8f; font-weight: 500; }
.dropdown-header { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #999; }
.btn-outline-secondary { border-color: #d6d6d6; color: #555; padding: 0.5rem 1rem; font-size: 0.85rem; }
.btn-outline-secondary:hover { background-color: #f8f9fa; border-color: #bbb; color: #333; }
.btn-outline-secondary:focus { box-shadow: none; border-color: #3f3f8f; }
.badge.bg-light { background-color: #f8f9fa !important; border: 1px solid #d6d6d6; font-weight: normal; font-size: 0.85rem; }
.badge.bg-light a:hover { color: #c0292b !important; }

/* Modal */
.trk-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.45); z-index: 9999;
    align-items: center; justify-content: center;
    padding: 16px; backdrop-filter: blur(2px);
}
.trk-modal-overlay.open { display: flex; }

.trk-modal {
    background: #fff; border-radius: 12px;
    width: 100%; max-width: 520px;
    max-height: calc(100vh - 40px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.22);
    display: flex; flex-direction: column;
    animation: modalIn 0.22s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(16px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.trk-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px 14px; border-bottom: 1px solid #ebebeb; flex-shrink: 0;
}
.trk-modal-title { display: flex; align-items: center; gap: 10px; }
.trk-modal-type  { font-size: 0.78rem; font-weight: 800; letter-spacing: 0.07em; text-transform: uppercase; }
.trk-modal-id    { font-size: 0.72rem; color: #aaa; font-weight: 600; }
.trk-modal-close {
    background: none; border: none; font-size: 1rem; color: #888;
    cursor: pointer; padding: 2px 7px; border-radius: 4px;
    transition: background 0.15s; flex-shrink: 0;
}
.trk-modal-close:hover { background: #f0f0f0; color: #333; }

.trk-modal-statusbar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px; padding: 10px 20px;
    background: #fafafa; border-bottom: 1px solid #ebebeb; flex-shrink: 0;
}
.trk-modal-meta-dates { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.trk-modal-meta-sep   { color: #ccc; }
.trk-modal-meta-item  { display: flex; flex-direction: column; align-items: flex-end; }
.trk-modal-meta-label { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #bbb; line-height: 1.2; }
.trk-modal-meta-val   { font-size: 0.78rem; color: #555; font-weight: 500; }

.trk-modal-body {
    padding: 18px 20px; display: flex; flex-direction: column;
    gap: 16px; overflow-y: auto; flex: 1; min-height: 0; max-height: 60vh;
}

.trk-modal-section-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 0.68rem; font-weight: 800;
    letter-spacing: 0.07em; text-transform: uppercase;
    color: #888; margin-bottom: 8px; flex-shrink: 0;
}

.trk-modal-message-box {
    background: #f4f3ff; border: 1.5px solid #dddaf8;
    border-radius: 8px; padding: 12px 14px;
    font-size: 0.88rem; color: #333; line-height: 1.65;
    white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word;
    max-height: 300px; overflow-y: auto;
}
.trk-modal-admin-box {
    background: #e8e8e8; border-radius: 8px;
    padding: 12px 14px; max-height: 250px; overflow-y: auto;
}
.trk-modal-remarks { font-size: 0.88rem; color: #333; line-height: 1.65; margin: 0; white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word; }
.trk-modal-eval-by { font-size: 0.75rem; color: #888; margin: 6px 0 0; }

.trk-modal-footer {
    padding: 12px 20px 16px; border-top: 1px solid #ebebeb;
    display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0;
}
.trk-modal-close-btn {
    background: #3f3f8f; color: white; border: none;
    border-radius: 7px; padding: 7px 24px;
    font-size: 0.83rem; font-weight: 600; cursor: pointer; transition: background 0.15s;
}
.trk-modal-close-btn:hover { background: #2e2e70; }
</style>

<script>
function openModal(btn) {
    var card = btn.closest('.trk-card');

    document.getElementById('modal-id').textContent             = '#' + card.dataset.id;
    document.getElementById('modal-date-submitted').textContent = card.dataset.dateSubmitted;
    document.getElementById('modal-message').textContent        = card.dataset.message;

    document.getElementById('modal-date-actioned-label').textContent = card.dataset.dateLabel;
    document.getElementById('modal-date-actioned').textContent       = card.dataset.dateActioned;

    var typeEl = document.getElementById('modal-type-label');
    typeEl.textContent = card.dataset.type;
    typeEl.className   = 'trk-modal-type ' + card.dataset.typeCss;

    var statusEl = document.getElementById('modal-status-badge');
    statusEl.innerHTML = svgCheck() + ' ' + card.dataset.statusLabel;
    statusEl.className = 'trk-status ' + card.dataset.statusCss;

    var remarks   = card.dataset.remarks;
    var evalBy    = card.dataset.evaluatedBy;
    var remarksEl = document.getElementById('modal-remarks');
    var evalEl    = document.getElementById('modal-evaluated-by');

    if (remarks) {
        remarksEl.textContent = remarks;
        evalEl.textContent    = evalBy ? '— ' + evalBy : '';
    } else {
        remarksEl.innerHTML = '<em style="color:#aaa;font-size:0.83rem;">'
            + (card.dataset.status === 'pending' ? 'Awaiting review — no response yet.' : 'No remarks were added.')
            + '</em>';
        evalEl.textContent = '';
    }

    document.getElementById('trkModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('trkModal').classList.remove('open');
    document.body.style.overflow = '';
}

function closeModalOutside(e) {
    if (e.target === document.getElementById('trkModal')) closeModal();
}

function svgCheck() {
    return '<svg width="13" height="13" viewBox="0 0 14 14" fill="none" style="vertical-align:-2px;margin-right:4px">'
        + '<circle cx="7" cy="7" r="6.3" stroke="currentColor" stroke-width="1.4"/>'
        + '<path d="M4.5 7l2 2 3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'
        + '</svg>';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
@endsection