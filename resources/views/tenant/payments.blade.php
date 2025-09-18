@extends('layouts.tenantdashboardlayout')

@section('title', 'My Payments')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-light">Payment History</div>
    <div class="card-body">
        @if($payments->isEmpty())
            <p>No payments found.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td>{{ ucfirst($payment->type) }}</td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
