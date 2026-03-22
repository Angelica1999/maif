@extends('layouts.app')

@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="card-title mb-1">
                        Conversation with {{ $recommendation->user ? $recommendation->user->fname . ' ' . $recommendation->user->lname : 'User' }}
                    </h4>
                    <p class="card-description mb-0">
                        #{{ $recommendation->id }} - {{ ucfirst($recommendation->type) }}
                        @php
                            $statusColor = match($recommendation->status) {
                                'approved' => 'success',
                                'in_progress' => 'info',
                                'done' => 'primary',
                                'rejected' => 'danger',
                                default => 'warning'
                            };
                            $statusLabel = match($recommendation->status) {
                                'in_progress' => 'In Progress',
                                'done' => 'Done',
                                default => ucfirst($recommendation->status)
                            };
                        @endphp
                        <span class="badge bg-{{ $statusColor }} ms-2">
                            {{ $statusLabel }}
                        </span>
                        <span class="badge bg-info ms-1">{{ ucfirst($recommendation->type) }}</span>
                    </p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    ← Back to Reports
                </a>
            </div>

            <div class="chat-container" id="chatContainer" style="max-height: 500px; overflow-y: auto; padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
                <!-- Original Message -->
                <div class="chat-message original-message mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="chat-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; font-weight: bold; background: #3f3f8f !important;">
                            {{ substr($recommendation->user->fname ?? 'U', 0, 1) }}
                        </div>
                        <div class="ms-3">
                            <strong>{{ $recommendation->user ? $recommendation->user->fname . ' ' . $recommendation->user->lname : 'User' }}</strong>
                            <small class="text-muted ms-2">{{ $recommendation->created_at->format('M d, Y H:i') }}</small>
                            @if($recommendation->evaluated_by && $recommendation->status !== 'pending')
                                <span class="badge bg-secondary ms-2">Initially evaluated by: {{ $recommendation->evaluated_by }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="chat-content p-3 rounded" style="background: white; margin-left: 50px; border-left: 4px solid #3f3f8f; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        {{ $recommendation->recommendation }}
                        @if($recommendation->remarks)
                            <div class="mt-3 pt-2 border-top">
                                <strong>Initial Remarks:</strong>
                                <p class="mb-0 text-muted">{{ $recommendation->remarks }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Replies -->
                @foreach($recommendation->replies as $reply)
                    @php $isAdmin = !empty($reply->evaluated_by); @endphp
                    <div class="chat-message reply-message mb-4 {{ $isAdmin ? 'admin-reply' : 'user-reply' }}"
                         data-reply-id="{{ $reply->id }}">
                        <div class="d-flex align-items-center mb-2 {{ $isAdmin ? 'flex-row-reverse' : '' }}">
                            <div class="chat-avatar {{ $isAdmin ? 'bg-success' : 'bg-info' }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px; font-weight: bold; background: {{ $isAdmin ? '#28a745' : '#17a2b8' }} !important;">
                                {{ $isAdmin ? substr($reply->evaluated_by, 0, 1) : substr($reply->user->fname ?? 'U', 0, 1) }}
                            </div>
                            <div class="{{ $isAdmin ? 'me-3 text-end' : 'ms-3' }}">
                                <strong>
                                    @if($isAdmin)
                                        Admin ({{ $reply->evaluated_by }})
                                    @else
                                        {{ $reply->user->fname ?? 'User' }} {{ $reply->user->lname ?? '' }}
                                    @endif
                                </strong>
                                <small class="text-muted ms-2">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                                @if($isAdmin)
                                    <span class="badge bg-success ms-2">Staff Reply</span>
                                @endif
                            </div>
                        </div>
                        <div class="chat-content p-3 rounded" 
                             style="background: {{ $isAdmin ? '#f1f8e9' : '#e3f2fd' }}; 
                                    margin-{{ $isAdmin ? 'right' : 'left' }}: 50px;
                                    border-{{ $isAdmin ? 'right' : 'left' }}: 4px solid {{ $isAdmin ? '#4caf50' : '#2196f3' }};
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            {{ $reply->recommendation }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Admin Reply Form - Only show for pending bugs -->
            @if($recommendation->type === 'bug' && $recommendation->status === 'pending')
                <div class="reply-form mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Reply as Admin</h5>
                            <p class="text-muted small mb-3">
                                Replying as: <strong>{{ auth()->user()->fname }} {{ auth()->user()->lname }}</strong><br>
                                <small class="text-info">Note: Status will remain as <strong>Pending</strong></small>
                            </p>

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form id="adminReplyForm" method="POST" action="{{ route('admin.reports.reply.submit', $recommendation->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <textarea 
                                        name="message" 
                                        rows="3" 
                                        class="form-control @error('message') is-invalid @enderror" 
                                        placeholder="Type your reply here..." 
                                        required
                                        id="replyMessage"
                                    >{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success" id="submitReply">
                                        <span id="submitBtnText">Send Reply</span>
                                        <span id="submitBtnSpinner" class="spinner-border spinner-border-sm ms-1 d-none" role="status"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($recommendation->type === 'bug' && $recommendation->status !== 'pending')
                <!-- Show chat history but disable replies -->
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle"></i> This bug report is no longer pending. You can view the chat history but cannot send new replies.
                </div>
                
                <!-- Optional: Show evaluation summary -->
                <div class="card mt-3 bg-light">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Evaluation Summary</h6>
                        <p><strong>Status:</strong> <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span></p>
                        @if($recommendation->evaluated_by)
                            <p><strong>Evaluated by:</strong> {{ $recommendation->evaluated_by }}</p>
                        @endif
                        @if($recommendation->remarks)
                            <p><strong>Remarks:</strong> {{ $recommendation->remarks }}</p>
                        @endif
                    </div>
                </div>
            @elseif($recommendation->type !== 'bug')
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle"></i> This is a {{ $recommendation->type }}. Chat is only available for bug reports.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.chat-container::-webkit-scrollbar { width: 8px; }
.chat-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
.chat-container::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
.chat-container::-webkit-scrollbar-thumb:hover { background: #555; }
.chat-message { animation: fadeIn 0.3s ease; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const chatContainer = document.getElementById('chatContainer');
    const replyForm     = document.getElementById('adminReplyForm');
    const messageTA     = document.getElementById('replyMessage');
    const submitBtn     = document.getElementById('submitReply');
    const submitText    = document.getElementById('submitBtnText');
    const submitSpinner = document.getElementById('submitBtnSpinner');

    chatContainer.scrollTop = chatContainer.scrollHeight;

    const renderedIds = new Set(
        [...document.querySelectorAll('[data-reply-id]')].map(el => parseInt(el.dataset.replyId))
    );

    function appendReply(reply) {
        if (renderedIds.has(reply.id)) return;
        renderedIds.add(reply.id);

        const isAdmin     = !!reply.evaluated_by;
        const initial     = isAdmin ? reply.evaluated_by[0] : (reply.user_fname ? reply.user_fname[0] : 'U');
        const name        = isAdmin ? `Admin (${reply.evaluated_by})` : `${reply.user_fname ?? ''} ${reply.user_lname ?? ''}`;
        const bgColor     = isAdmin ? '#28a745' : '#17a2b8';
        const avatarClass = isAdmin ? 'bg-success' : 'bg-info';
        const bubbleBg    = isAdmin ? '#f1f8e9' : '#e3f2fd';
        const borderColor = isAdmin ? '#4caf50' : '#2196f3';
        const flexDir     = isAdmin ? 'flex-row-reverse' : '';
        const marginSide  = isAdmin ? 'margin-right:50px;border-right' : 'margin-left:50px;border-left';
        const textAlign   = isAdmin ? 'me-3 text-end' : 'ms-3';
        const badge       = isAdmin ? '<span class="badge bg-success ms-2">Staff Reply</span>' : '';
        const text        = (reply.recommendation ?? '').replace(/\n/g, '<br>');

        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message reply-message mb-4 ${isAdmin ? 'admin-reply' : 'user-reply'}`;
        msgDiv.dataset.replyId = reply.id;
        msgDiv.innerHTML = `
            <div class="d-flex align-items-center mb-2 ${flexDir}">
                <div class="chat-avatar ${avatarClass} text-white rounded-circle d-flex align-items-center justify-content-center"
                     style="width:40px;height:40px;font-weight:bold;background:${bgColor} !important;">
                    ${initial}
                </div>
                <div class="${textAlign}">
                    <strong>${name}</strong>
                    <small class="text-muted ms-2">${reply.created_at}</small>
                    ${badge}
                </div>
            </div>
            <div class="chat-content p-3 rounded"
                 style="background:${bubbleBg};${marginSide}:4px solid ${borderColor};box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                ${text}
            </div>
        `;

        chatContainer.appendChild(msgDiv);
        chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
    }

    // Submit reply via AJAX - only if form exists
    if (replyForm) {
        replyForm.addEventListener('submit', function (e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitText.textContent = 'Sending...';
            submitSpinner.classList.remove('d-none');

            fetch(replyForm.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(replyForm)
            })
            .then(async res => {
                const text = await res.text();
                try { return JSON.parse(text); }
                catch { throw new Error('Unexpected server response.'); }
            })
            .then(data => {
                if (data.error) {
                    alert(typeof data.error === 'object' ? JSON.stringify(data.error) : data.error);
                } else {
                    messageTA.value = '';
                    appendReply({
                        id:             data.reply.id,
                        user_id:        data.reply.user_id,
                        evaluated_by:   data.reply.evaluated_by,
                        recommendation: data.reply.recommendation,
                        created_at:     data.reply.created_at
                    });
                }
            })
            .catch(err => alert('Error: ' + err.message))
            .finally(() => {
                submitBtn.disabled = false;
                submitText.textContent = 'Send Reply';
                submitSpinner.classList.add('d-none');
            });
        });
    }

    // Poll for new messages - only if the report is still pending
    @if($recommendation->type === 'bug' && $recommendation->status === 'pending')
    function poll() {
        fetch('{{ route("admin.reports.poll", $recommendation->id) }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => { if (data.replies) data.replies.forEach(appendReply); })
        .catch(() => {});
    }

    setInterval(poll, 1000);
    @endif
});
</script>
@endsection