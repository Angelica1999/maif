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
                <form method="GET" class="d-flex align-items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Statuses</option>
                        <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @if (request('status'))
                        <a href="{{ route('admin.reports.index') }}" class="text-secondary text-decoration-none small">&#10005; Clear</a>
                    @endif
                </form>
            </div>
            
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if ($recommendations->isEmpty())
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
                            @foreach ($recommendations as $rec)
                                @php
                                    $badgeClass = match($rec->status) {
                                        'approved' => 'badge bg-success',
                                        'rejected' => 'badge bg-danger',
                                        default    => 'badge bg-warning text-dark',
                                    };

                                    $typeBadgeClass = match($rec->type ?? 'recommendation') {
                                        'bug'            => 'badge bg-danger',
                                        'recommendation' => 'badge bg-info',
                                        default          => 'badge bg-secondary',
                                    };

                                    $fullText      = $rec->recommendation ?? '';
                                    $hasReply      = str_contains($fullText, '--- Your Reply ---');
                                    $parts         = explode("\n\n--- Your Reply ---\n", $fullText, 2);
                                    $originalText  = trim($parts[0] ?? $fullText);
                                    $replyText     = isset($parts[1]) ? trim($parts[1]) : null;

                                    $isEvaluated = $rec->status !== 'pending';
                                    
                                    // Count replies
                                    $replyCount = $rec->replies()->count();
                                @endphp

                                <tr>
                                    <td class="small">
                                        {{ $rec->user ? $rec->user->fname . ' ' . $rec->user->lname : '—' }}
                                    </td>
                                    <td>
                                        <span class="{{ $badgeClass }}">{{ ucfirst($rec->status) }}</span>
                                    </td>
                                    <td>
                                        <span class="{{ $typeBadgeClass }}">
                                            {{ ucfirst($rec->type ?? 'recommendation') }}
                                        </span>
                                    </td>
                                    <td class="small" style="max-width: 200px;">
                                        <div style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"
                                             title="{{ $originalText }}">
                                            {{ $originalText }}
                                        </div>
                                    </td>
                                    <td class="small text-muted" style="max-width: 200px;">
                                        @if ($rec->remarks)
                                            <div style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"
                                                 title="{{ $rec->remarks }}">
                                                {{ $rec->remarks }}
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small">{{ $rec->evaluated_by ?? '—' }}</td>
                                    <td class="small text-nowrap">{{ $rec->created_at->format('M d, Y') }}</td>
                                    <td>
                                         <div class= "d-flex" >
                                            @if($rec->status !== 'pending' || $replyCount > 0)
                                                <a href="{{ route('admin.reports.conversation', $rec->id) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="View Conversation" style="width: 80px; border-radius: 0px;">
                                                    Chat @if($replyCount > 0)({{ $replyCount }})@endif
                                                </a>
                                            @endif
                                            
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning"
                                                style="width: 80px; color: white; border-radius: 0px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#evaluateModal{{ $rec->id }}">
                                                {{ $isEvaluated ? 'View' : 'Evaluate' }}
                                            </button>

                                            <form action="{{ route('admin.reports.destroy', $rec->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this record? All replies will also be deleted.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" style="width: 80px; border-radius: 0px;">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="evaluateModal{{ $rec->id }}" tabindex="-1"
                                    aria-labelledby="evaluateModalLabel{{ $rec->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="evaluateModalLabel{{ $rec->id }}">
                                                    {{ $isEvaluated ? 'View' : 'Evaluate' }} {{ ucfirst($rec->type ?? 'recommendation') }} #{{ $rec->id }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <form action="{{ route('admin.reports.evaluate', $rec->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    @if ($isEvaluated)
                                                        <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                                                            <i class="bi bi-info-circle-fill"></i>
                                                            <span>This record has already been <strong>{{ $rec->status }}</strong> and can no longer be modified.</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="mb-3 p-3 bg-light rounded">
                                                        <p class="mb-1"><strong>Name:</strong>
                                                            {{ $rec->user ? $rec->user->fname . ' ' . $rec->user->lname : '—' }}
                                                        </p>
                                                        <p class="mb-1"><strong>Type:</strong>
                                                            <span class="{{ $typeBadgeClass }}">
                                                                {{ ucfirst($rec->type ?? 'recommendation') }}
                                                            </span>
                                                        </p>
                                                        <p class="mb-0"><strong>Submission Date:</strong>
                                                            {{ $rec->created_at->format('M d, Y H:i') }}
                                                        </p>
                                                        @if($replyCount > 0)
                                                            <p class="mb-0 mt-1"><strong>Replies:</strong>
                                                                <span class="badge bg-secondary">{{ $replyCount }} reply/replies</span>
                                                                <a href="{{ route('admin.reports.conversation', $rec->id) }}" class="ms-2">View Conversation</a>
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
                                                                <option value="pending"  {{ $rec->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                                                <option value="approved" {{ $rec->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                                <option value="rejected" {{ $rec->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                            </select>
                                                            @if ($isEvaluated)
                                                                <input type="hidden" name="status" value="{{ $rec->status }}">
                                                            @endif
                                                        </div>

                                                        <!-- <div class="col-12">
                                                            <label class="form-label fw-semibold">Evaluated By</label>
                                                            <input
                                                                type="text"
                                                                name="evaluated_by"
                                                                value="{{ $rec->evaluated_by ?? Auth::user()->name }}"
                                                                placeholder="Your name"
                                                                class="form-control"
                                                                {{ $isEvaluated ? 'disabled' : '' }}>
                                                            @if ($isEvaluated)
                                                                <input type="hidden" name="evaluated_by" value="{{ $rec->evaluated_by }}">
                                                            @endif
                                                        </div> -->

                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Remarks</label>
                                                            <textarea
                                                                name="remarks"
                                                                rows="3"
                                                                placeholder="Add your remarks..."
                                                                class="form-control"
                                                                {{ $isEvaluated ? 'disabled' : '' }}>{{ $rec->remarks }}</textarea>
                                                            @if ($isEvaluated)
                                                                <input type="hidden" name="remarks" value="{{ $rec->remarks }}">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        {{ $isEvaluated ? 'Close' : 'Cancel' }}
                                                    </button>
                                                    @if (!$isEvaluated)
                                                        <button type="submit" class="btn btn-primary">Save Evaluation</button>
                                                    @else
                                                        <a href="{{ route('admin.reports.conversation', $rec->id) }}" class="btn btn-success me-2">
                                                            View Conversation
                                                        </a>
                                                        <button type="button" class="btn btn-secondary" disabled>Already Evaluated</button>
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
@endsection