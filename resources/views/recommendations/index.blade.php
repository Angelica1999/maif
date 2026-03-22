@extends('layouts.app')

@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title mb-0">Manage Recommendation/Bugs</h4>
                    <p class="card-description mb-0">MAIF-IPP</p>
                </div>

                <div class="mt-3 mt-md-0">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center"
                            type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                <path d="M2 4H14M4 8H12M6 12H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <circle cx="12" cy="4" r="1.5" fill="currentColor" stroke="white" stroke-width="1" />
                                <circle cx="8" cy="8" r="1.5" fill="currentColor" stroke="white" stroke-width="1" />
                                <circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="white" stroke-width="1" />
                            </svg>
                            Filter:
                            <span id="selected-filter" class="ms-1 fw-normal">
                                @if(request('status') == 'approved') Approved
                                @elseif(request('status') == 'in_progress') In Progress
                                @elseif(request('status') == 'done') Done
                                @elseif(request('status') == 'rejected') Rejected
                                @elseif(request('type') == 'bug') Bugs
                                @elseif(request('type') == 'recommendation') Recommendations
                                @else All Items
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown" style="min-width: 200px;">
                            <li>
                                <a class="dropdown-item {{ !request('status') && !request('type') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                    <div class="d-flex align-items-center">All Items</div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">By Status</h6></li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'approved' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'approved']) }}">
                                    <div class="d-flex align-items-center">Approved</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'in_progress' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'in_progress']) }}">
                                    <div class="d-flex align-items-center">In Progress</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'done' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'done']) }}">
                                    <div class="d-flex align-items-center">Done</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'rejected' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'rejected']) }}">
                                    <div class="d-flex align-items-center">Rejected</div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">By Type</h6></li>
                            <li>
                                <a class="dropdown-item {{ request('type') == 'recommendation' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['type' => 'recommendation']) }}">
                                    <div class="d-flex align-items-center">Recommendations</div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('type') == 'bug' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['type' => 'bug']) }}">
                                    <div class="d-flex align-items-center">Bugs</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($recommendations->isEmpty())
                <div class="alert alert-danger" role="alert" style="width: 100%; margin-top:50px">
                    <i class="typcn typcn-times menu-icon"></i>
                    <strong>No recommendation or bugs found</strong>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>BUGS/RECOMMENDATION</th>
                                <th>Remarks</th>
                                <th>Evaluated by</th>
                                <th>Date</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recommendations as $rec)
                                @php
                                      $badgeStyle = match($rec->status) {
                                        'approved'    => 'background-color: #58961e;',
                                        'in_progress' => 'background-color: #e08309;',
                                        'done'        => 'background-color: #0a9638;',
                                        'rejected'    => 'background-color: #f80e25;',
                                        default       => 'background-color: #d3b300;',
                                    };

                                    $statusLabel = match($rec->status) {
                                        'in_progress' => 'In Progress',
                                        'done'        => 'Done',
                                        default       => ucfirst($rec->status),
                                    };

                                    $typeBadgeStyle = match($rec->type ?? 'recommendation') {
                                        'bug'            => 'background-color: #aa3a31;',
                                        'recommendation' => 'background-color: #126ec5;',
                                        default          => 'background-color: #6c757d;',
                                    };

                                    $fullText     = $rec->recommendation ?? '';
                                    $parts        = explode("\n\n--- Your Reply ---\n", $fullText, 2);
                                    $originalText = trim($parts[0] ?? $fullText);

                                    $isEvaluated = match(true) {
                                        $rec->status === 'done'                                        => true,
                                        $rec->status === 'rejected'                                    => true,
                                        
                                        default                                                        => false,
                                    };

                                    $replyCount = $rec->replies()->count();
                                    $showChat   = ($rec->type ?? '') === 'bug' && $rec->status === 'pending';
                                @endphp

                                <tr>
                                    <td class="small">
                                        {{ $rec->user ? $rec->user->fname . ' ' . $rec->user->lname : 'N/A' }}
                                    </td>
                                    <td>
                                       <span class="badge" style="{{ $badgeStyle }} color: #fff;">
                                            {{ $statusLabel }}
                                    </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="{{ $typeBadgeStyle }} color: #fff;">
                                            {{ ucfirst($rec->type ?? 'recommendation') }}
                                        </span>
                                    </td>
                                    <td class="small" style="max-width: 200px;">
                                        <div style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="{{ $originalText }}">
                                            {{ $originalText }}
                                        </div>
                                    </td>
                                    <td class="small text-muted" style="max-width: 200px;">
                                        @if($rec->remarks)
                                            <div style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="{{ $rec->remarks }}">
                                                {{ $rec->remarks }}
                                            </div>
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $rec->evaluated_by ?? 'N/A' }}</td>
                                    <td class="small text-nowrap">{{ $rec->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($showChat)
                                                <a href="{{ route('admin.reports.conversation', $rec->id) }}"
                                                   class="btn btn-sm btn-success"
                                                   title="View Conversation"
                                                   style="width: 80px; border-radius: 0px;">
                                                    Chat{{ $replyCount > 0 ? ' ('.$replyCount.')' : '' }}
                                                </a>
                                            @endif

                                            <button
                                                type="button"
                                                class="btn btn-sm 
                                                @if($rec->status === 'pending')
                                                    btn-secondary
                                                @elseif($isEvaluated)
                                                    btn-warning
                                                @else
                                                    btn-info
                                                @endif"
                                                style="width: 80px; color: white; border-radius: 0px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#evaluateModal{{ $rec->id }}">
                                                @if($rec->status === 'pending')
                                                    Evaluate
                                                @elseif($isEvaluated)
                                                    View
                                                @else
                                                    Update
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <div class="modal fade" id="evaluateModal{{ $rec->id }}" tabindex="-1"
                                    aria-labelledby="evaluateModalLabel{{ $rec->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="evaluateModalLabel{{ $rec->id }}">
                                                    @if($rec->status === 'pending')
                                                        Evaluate
                                                    @elseif($isEvaluated)
                                                        View
                                                    @else
                                                        Update
                                                    @endif
                                                    {{ ucfirst($rec->type ?? 'recommendation') }} #{{ $rec->id }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <form action="{{ route('admin.reports.evaluate', $rec->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">

                                                    @if($isEvaluated)
                                                        <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                                                            <i class="bi bi-info-circle-fill"></i>
                                                            <span>This record is <strong>{{ $statusLabel }}</strong> and can no longer be modified.</span>
                                                        </div>
                                                    @endif

                                                    <div class="mb-3 p-3 bg-light rounded">
                                                        <p class="mb-1"><strong>Name:</strong>
                                                            {{ $rec->user ? $rec->user->fname . ' ' . $rec->user->lname : 'N/A' }}
                                                        </p>
                                                        <p class="mb-1"><strong>Type:</strong>
                                                            <span class="{{ $typeBadgeStyle}}">
                                                                {{ ucfirst($rec->type ?? 'recommendation') }}
                                                            </span>
                                                        </p>
                                                        <p class="mb-0"><strong>Submission Date:</strong>
                                                            {{ $rec->created_at->format('M d, Y H:i') }}
                                                        </p>
                                                        @if($replyCount > 0)
                                                            <p class="mb-0 mt-1">
                                                                <strong>Replies:</strong>
                                                                <span class="badge bg-secondary">{{ $replyCount }} reply/replies</span>
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Original Message</label>
                                                        <div class="p-3 rounded" style="background:#f4f3ff;border:1.5px solid #dddaf8;font-size:0.88rem;line-height:1.65;white-space:pre-wrap;word-break:break-word;">{{ $originalText }}</div>
                                                    </div>

                                                    <hr>

                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Status</label>
                                                            <select name="status" class="form-select" {{ $isEvaluated ? 'disabled' : '' }}>
                                                                @if($rec->status === 'pending')
                                                                    <option value="approved">Approved</option>
                                                                    <option value="rejected">Rejected</option>
                                                                @elseif($rec->status === 'approved' && $rec->type === 'bug')
                                                                    <option value="in_progress">In Progress</option>
                                                                    <option value="done">Done</option>
                                                              @elseif($rec->status === 'approved' && $rec->type === 'recommendation')
                                                                    <option value="in_progress">In Progress</option>
                                                                    <option value="done">Done</option>
                                                                @elseif($rec->status === 'in_progress' && $rec->type === 'recommendation')
                                                                   
                                                                    <option value="done">Done</option> 
                                                                @elseif($rec->status === 'in_progress')
                                                                    <option value="done">Done</option>
                                                                @elseif($rec->status === 'done')
                                                                    <option value="done" selected>Done</option>
                                                                @elseif($rec->status === 'rejected')
                                                                    <option value="rejected" selected>Rejected</option>
                                                                @endif
                                                            </select>
                                                            @if($isEvaluated)
                                                                <input type="hidden" name="status" value="{{ $rec->status }}">
                                                            @endif
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Remarks</label>
                                                            <textarea
                                                                name="remarks"
                                                                rows="3"
                                                                placeholder="Add your remarks..."
                                                                class="form-control"
                                                                {{ $isEvaluated ? 'disabled' : '' }}>{{ $rec->remarks }}</textarea>
                                                            @if($isEvaluated)
                                                                <input type="hidden" name="remarks" value="{{ $rec->remarks }}">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        {{ $isEvaluated ? 'Close' : 'Cancel' }}
                                                    </button>

                                                    @if($replyCount > 0 || $rec->type === 'bug')
                                                        <a href="{{ route('admin.reports.conversation', $rec->id) }}"
                                                           class="btn btn-info">
                                                            <i class="bi bi-chat-dots"></i>
                                                            View Conversation{{ $replyCount > 0 ? ' ('.$replyCount.')' : '' }}
                                                        </a>
                                                    @endif

                                                    @if(!$isEvaluated)
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    @else
                                                        <button type="button" class="btn btn-secondary" disabled>Locked</button>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3" id="pagination_links">
                    {{ $recommendations->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.type-icon { font-size: 1rem; line-height: 1; }
.type-rec  { color: #c07a00; }
.type-bug  { color: #c0292b; }
.dropdown-item.active { background-color: #f0f0ff; color: #3f3f8f; font-weight: 500; }
.dropdown-header { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #999; }
.btn-outline-secondary { border-color: #d6d6d6; color: #555; padding: 0.5rem 1rem; font-size: 0.85rem; }
.btn-outline-secondary:hover { background-color: #f8f9fa; border-color: #bbb; color: #333; }
.btn-outline-secondary:focus { box-shadow: none; border-color: #3f3f8f; }
.bi-chat-dots { margin-right: 4px; }
</style>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush
@endsection