@extends('layouts.managerdashboardlayout')

@section('title', 'Manage Tenants')
@section('page-title', 'Manage Tenants')

@section('content')

{{-- Pending Tenants Approve/Reject --}}
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        Pending Tenant Applications
    </div>
    <div class="card-body">
        @if($pendingTenants->isEmpty())
            <p>No pending applications.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingTenants as $tenant)
                        <tr>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->email }}</td>
                            <td>
                                <form action="{{ route('manager.approve', $tenant->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('manager.reject', $tenant->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Approved Tenants List --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        Approved Tenants
    </div>
    <div class="card-body">
        @if($approvedTenantList->isEmpty())
            <p>No approved tenants.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedTenantList as $tenant)
                        <tr>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection
