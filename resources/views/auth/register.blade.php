@extends('layouts.app') {{-- use your base layout --}}

@section('title', 'Register')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Register</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                    class="form-control" required
                    pattern="[a-zA-Z0-9]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                    title="Only letters/numbers before @, valid domain after @">
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact" class="form-control" 
                    required maxlength="15" minlength="10"
                    pattern="^[0-9]{10,15}$"
                    title="Contact must be 10–15 digits only"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">

            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$"
                    title="At least 8 characters, with uppercase, lowercase, number & special character">
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Register</button>
        </form>

        <hr>

        <div class="text-center">
            <p class="mb-2">Already have an account?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Login</a>
        </div>
    </div>
</div>
@endsection
