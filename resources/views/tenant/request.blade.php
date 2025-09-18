@extends('layouts.tenantdashboardlayout')

@section('title', 'My Requests')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-light">Maintenance Requests</div>
    <div class="card-body">
        @if($requests->isEmpty())
            <p>No requests found.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</td>
                            <td>{{ ucfirst($request->type) }}</td>
                            <td>{{ ucfirst($request->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
