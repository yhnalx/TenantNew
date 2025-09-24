@extends('layouts.tenantdashboardlayout')

@section('title', 'My Payments')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span>Payment History</span>
        <!-- Make Payment Button -->
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#makePaymentModal">
            Make Payment
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($payments->isEmpty())
            <p>No payments found.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>For</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Account No.</th>
                        <th>Status</th>
                        <th>Proof</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->pay_date ?? $payment->created_at)->format('M d, Y') }}</td>
                            <td>{{ $payment->payment_for ?? '-' }}</td>
                            <td>{{ ucfirst($payment->pay_method) }}</td>
                            <td>₱{{ number_format($payment->pay_amount, 2) }}</td>
                            <td>{{ $payment->account_no ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->pay_status === 'Paid' ? 'success' : 'warning' }}">
                                    {{ $payment->pay_status }}
                                </span>
                            </td>
                            <td>
                                @if($payment->proof)
                                    <a href="{{ asset('storage/'.$payment->proof) }}" target="_blank">View</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('tenant.payments.store') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="makePaymentModalLabel">Make a Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select name="pay_method" id="payment_method" class="form-select" required>
                        <option value="">Select Method</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>


                <!-- Payment For -->
                <div class="mb-3">
                    <label class="form-label">Payment For</label>
                    <select name="payment_for" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Rent">Rent</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Deposit">Deposit</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Amount -->
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="pay_amount" class="form-control" value="2500" required>
                </div>

                <!-- Extra Field for Online Payments -->
                <div class="mb-3 d-none" id="accountNumberField">
                    <label class="form-label">Account / GCash Number</label>
                    <input type="text" name="account_no" class="form-control">
                </div>

                <!-- Proof of Payment -->
                <div class="mb-3">
                    <label class="form-label">Proof of Payment (Screenshot / Receipt)</label>
                    <input type="file" name="proof" class="form-control" accept="image/*">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Submit Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- Script to toggle Account Number -->
<script>
    document.getElementById('payment_method').addEventListener('change', function() {
        let accountField = document.getElementById('accountNumberField');
        if (this.value === 'GCash' || this.value === 'Bank Transfer') {
            accountField.classList.remove('d-none');
        } else {
            accountField.classList.add('d-none');
        }
    });
</script>
@endsection
