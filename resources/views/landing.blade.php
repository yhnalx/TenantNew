@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5 text-dark">
            🏠 Welcome to <span class="text-primary">Our Apartment Portal</span>
        </h1>
        <p class="lead text-muted">
            Find your next home with ease — we currently have 
            <strong class="text-success">{{ $vacantCount }}</strong> vacant rooms available.
        </p>
    </div>

    <!-- Units Grid -->
    <div class="row g-4">
        @foreach($units as $unit)
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 hover-card">
                    <div class="card-body text-center p-4">
                        <h5 class="card-title fw-semibold text-dark">{{ $unit->name }}</h5>
                        <span class="badge px-3 py-2 fs-6 rounded-pill 
                            {{ $unit->status === 'vacant' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($unit->status) }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="text-center mt-5">
        @if (Route::has('login'))
            @auth
                <a href="{{ route('tenant.home') }}" class="btn btn-lg btn-primary px-5 py-2 rounded-pill shadow-sm">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-lg btn-outline-primary px-5 py-2 rounded-pill me-2 shadow-sm">
                    Log in
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-lg btn-success px-5 py-2 rounded-pill shadow-sm">
                        Register
                    </a>
                @endif
            @endauth
        @endif
    </div>
</div>

<!-- Extra Styling -->
@push('styles')
<style>
    body {
        background: #f9fafc;
    }
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
</style>
@endpush
@endsection
