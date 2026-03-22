@extends('layouts.app')
@section('content')
<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4 class="card-title">RECOMMENDATION/ BUGS</h4>
                    <p class="card-description">MAIF-IPP</p>
                </div>
                        <a href="{{ route('recommendations.view') }}" class="btn btn-sm btn-warning" style="color: white;">
                            <i class="typcn typcn-lightbulb"></i> View Report
                        </a>
                </div>
                <div class="help-center-wrapper mt-4">
                    <div class="help-center-box text-center" >
                        <h2 class="help-title" style="margin-top: -20px;">How can we help?</h2>
                        <p class="help-subtitle">Choose what you'd like to submit.</p>

                        <div class="help-options d-flex flex-column flex-md-row justify-content-center gap-4 mt-4">

                            {{-- Report a Bug --}}
                            <a href="{{ route('bugs.create') }}" class="help-card text-decoration-none">
                                <div class="help-card-inner">
                                    <div class="help-icon mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
                                            viewBox="0 0 24 24" stroke="#3f3c3c" stroke-width="1.2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <h5 class="help-card-title">REPORT A BUGS</h5>
                                    <p class="help-card-desc">Something isn't working as expected? Let us know so we can fix it.</p>
                                </div>
                            </a>

                            {{-- Recommendation --}}
                            <a href="{{ route('recommendations.create') }}" class="help-card text-decoration-none">
                                <div class="help-card-inner">
                                    <div class="help-icon mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
                                            viewBox="0 0 24 24" stroke="#3f3c3c" stroke-width="1.2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </div>
                                    <h5 class="help-card-title">Recommendation</h5>
                                    <p class="help-card-desc">Have an idea to improve the system? We'd love to hear it.</p>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

<style>
    .help-center-wrapper {
        display: flex;
        justify-content: center;
        padding: 8px 0 12px;
        height: 500px;
    }

    .help-center-box {
        background: #faf9f7;
        padding: 70px 80px;
        width: 100%;
        border: 1px solid #c4c4c4;
    }

    .help-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 15px;
    }

    .help-subtitle {
        font-size: 1rem;
        color: #797575;
        margin-bottom: 15px;
    }

    .help-options {
        gap: 40px !important;
    }

    .help-card {
        margin-top: 30px;
        flex: 1;
        max-width: 600px;
        height: 200px;
        border: 2px outset #949393;
        border-radius: 2px;
        background: #f3f1f1;
        color: inherit;
        transition: all 0.3s ease; 
    }

    .help-card:hover {
        box-shadow: 0 8px 20px rgba(44, 65, 47, 0.6);
        border-color: #2f302f;
        transform: scale(1.03);
    }

    .help-card-inner {
        padding: 28px 20px 24px;
        text-align: center;
    }

    .help-icon {
        display: flex;
        justify-content: center;
    }

    .help-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #222;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .help-card-desc {
        font-size: 1rem;
        color: #666;
        line-height: 1.5;
    }
</style>
@endsection