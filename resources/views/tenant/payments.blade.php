@extends('layouts.tenantdashboardlayout')

@section('title', 'My Payments')

@section('content')
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #ffb347, #ffcc33);">
            <div class="card-body">
                <h6 class="card-title fw-semibold">Unpaid Rent - {{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                <p class="card-text display-6 fw-bold">₱{{ number_format($unpaidRent, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #ff5f6d, #ffc371);">
            <div class="card-body">
                <h6 class="card-title fw-semibold">Unpaid Utilities - {{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                <p class="card-text display-6 fw-bold">₱{{ number_format($unpaidUtilities, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm rounded-4 border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0">
        <h5 class="mb-0 fw-bold">Payment History</h5>
        <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#makePaymentModal">
            Make Payment
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success rounded-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
        @endif

        @if($payments->isEmpty())
            <p class="text-muted">No payments found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle">
                    <thead class="table-light text-uppercase text-muted small">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Payment For</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Account No</th>
                            <th>Status</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->pay_date ?? $payment->created_at)->format('M d, Y') }}</td>
                            <td>{{ $payment->payment_for ?? '-' }}</td>
                            <td>{{ ucfirst($payment->pay_method) }}</td>
                            <td class="fw-semibold">₱{{ number_format($payment->pay_amount, 2) }}</td>
                            <td>{{ $payment->account_no ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $payment->pay_status === 'Accepted' ? 'success' : 'warning text-dark' }}">
                                    {{ $payment->pay_status }}
                                </span>
                            </td>
                            <td>
                                @if($payment->proof)
                                    <a href="{{ asset('storage/'.$payment->proof) }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill">View</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('tenant.payments.store') }}" enctype="multipart/form-data" class="modal-content rounded-4 shadow-lg border-0 p-3">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="makePaymentModalLabel">Make a Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                @php $user = auth()->user(); @endphp

                <div class="form-floating mb-3">
                    <select name="payment_for" id="payment_for" class="form-select" required>
                        @if($user->deposit_amount > 0)
                            <option value="Deposit" data-balance="{{ $user->deposit_amount }}" selected>
                                Deposit (₱{{ number_format($user->deposit_amount, 2) }})
                            </option>
                        @else
                            @if($user->rent_balance > 0)
                                <option value="Rent" data-balance="{{ $user->rent_balance }}">Rent (₱{{ number_format($user->rent_balance, 2) }})</option>
                            @endif
                            @if($user->utility_balance > 0)
                                <option value="Utilities" data-balance="{{ $user->utility_balance }}">Utilities (₱{{ number_format($user->utility_balance, 2) }})</option>
                            @endif
                        @endif
                        <option value="Other" data-balance="0">Other</option>
                    </select>
                    <label for="payment_for">Payment For</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="number" name="pay_amount" id="pay_amount" class="form-control" required>
                    <label for="pay_amount">Amount</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="pay_method" id="payment_method" class="form-select" required>
                        <option value="">Select Method</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                    <label for="payment_method">Payment Method</label>
                </div>

                <div class="form-floating mb-3 d-none" id="accountNumberField">
                    <input type="text" name="account_no" class="form-control">
                    <label>Account / GCash Number</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Proof of Payment (Screenshot / Receipt)</label>
                    <input type="file" name="proof" class="form-control rounded-3" accept="image/*">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Submit Payment</button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForSelect = document.getElementById('payment_for');
    const payAmountInput = document.getElementById('pay_amount');
    const paymentMethodSelect = document.getElementById('payment_method');
    const accountField = document.getElementById('accountNumberField');
    const modal = document.getElementById('makePaymentModal');

    // Disable Rent/Utilities if deposit exists
    const depositOption = paymentForSelect.querySelector('option[value="Deposit"]');
    if(depositOption) {
        Array.from(paymentForSelect.options).forEach(opt => {
            if(opt.value !== 'Deposit' && opt.value !== 'Other') {
                opt.disabled = true;
            }
        });
    }

    // Toggle account field
    paymentMethodSelect.addEventListener('change', () => {
        accountField.classList.toggle('d-none', !(paymentMethodSelect.value === 'GCash' || paymentMethodSelect.value === 'Bank Transfer'));
    });

    // Update amount based on selection
    function updateAmount() {
        const selectedOption = paymentForSelect.options[paymentForSelect.selectedIndex];
        const balance = parseFloat(selectedOption?.dataset.balance || 0);
        payAmountInput.value = balance > 0 ? balance : '';
        payAmountInput.min = balance > 0 ? 1 : 0;
    }
    paymentForSelect.addEventListener('change', updateAmount);

    // Default amount when modal opens
    modal.addEventListener('show.bs.modal', () => {
        let defaultOption = paymentForSelect.querySelector('option[value="Deposit"]') ||
                            paymentForSelect.querySelector('option[value="Rent"]') ||
                            paymentForSelect.querySelector('option[value="Utilities"]');
        if(defaultOption) {
            defaultOption.selected = true;
            updateAmount();
        }
    });
});
</script>
@endsection
