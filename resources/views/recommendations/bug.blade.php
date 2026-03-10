@extends('layouts.app')

@section('title', 'Submit a Recommendation')

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            {{-- Page Header --}}
            <div class="mb-3">
                <h4 class="card-title mb-1">BUGS</h4>
                <p class="card-description text-muted medium mb-0">MAIF-IPP</p>
            </div>

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="rec-form-wrapper" style="justify-content: center;">
                <div class="rec-form-card">
                    <form action="{{ route('bugs.store') }}" method="POST">
                        @csrf

                        {{-- Email --}}
                        <div class="rec-field-group">
                            <label class="rec-label">Email Address:</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', auth()->user()?->email) }}"
                                placeholder="Enter e-mail (you@example.com)"
                                class="rec-input @error('email') is-invalid @enderror"
                            >
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Recommendation --}}
                        <div class="rec-field-group">
                            <label class="rec-label">Report Bugs:</label>
                            <textarea
                                name="recommendation"
                                rows="5"
                                placeholder="What happened? How can we reproduce it?"
                                class="rec-textarea @error('recommendation') is-invalid @enderror"
                            >{{ old('recommendation') }}</textarea>
                            @error('recommendation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="rec-submit-wrapper">
                            <button type="submit" class="rec-submit-btn">
                                Submit Bug Report
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .rec-form-wrapper {
        display: flex;
        justify-content: flex-start;
        padding: 8px 0 12px;
        height: 500px;
    }

    .rec-form-card {
        background: #fff;
        border: 1.5px solid #d6d1cb;
        padding: 50px 30px 20px;
        width: 100%;
        max-width: auto;
        
    }

    /* Field group */
    .rec-field-group {
        margin-bottom: 30px;
    }

    .rec-label {
        display: flex;
        font-size: 1.2rem;
        font-weight: 500;
        color: #2c2c2c;
        margin-bottom: 8px;
    }

    /* Input */
    .rec-input {
        display: block;
        width: 100%;
        padding: 16px 18px;
        font-size: 0.85rem;
        color: #555;
        background-color: #ededea;
        border: none;
        border-radius: 6px;
        outline: none;
        letter-spacing: 0.04em;
        transition: background 0.2s;
    }

    .rec-input::placeholder {
        color: #535252;
        letter-spacing: 0.06em;
        font-size: 1rem;
    }

    .rec-input:focus {
        background-color: #e4e4e0;
        box-shadow: 0 0 0 2px rgba(74, 110, 93, 0.25);
    }

    /* Textarea */
    .rec-textarea {
        display: block;
        width: 100%;
        padding: 12px 14px;
        font-size: 1rem;
        color: #555;
        background-color: #ededea;
        border: none;
        border-radius: 6px;
        outline: none;
        resize: vertical;
        min-height: 180px;
        transition: background 0.2s;
       
    }

    .rec-textarea::placeholder {
        color: #535252;
        letter-spacing: 0.06em;
        font-size: 1rem;
    }

    .rec-textarea:focus {
        background-color: #e4e4e0;
        box-shadow: 0 0 0 2px rgba(74, 110, 93, 0.25);
    }

    .rec-submit-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }
    .rec-submit-btn {
        display: block;
        width: 85%;
        padding: 14px;
        background-color: #4a6e5d;
        color: #fff;
        font-size: 1.2rem;
        font-weight: 600;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        letter-spacing: 0.02em;
        transition: background-color 0.2s ease, transform 0.15s ease;
    }

    .rec-submit-btn:hover {
        background-color: #3b5a4c;
        transform: translateY(-1px);
    }

    .rec-submit-btn:active {
        background-color: #334f43;
        transform: translateY(0);
    }
</style>

@endsection