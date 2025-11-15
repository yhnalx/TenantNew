@extends('layouts.tenantdashboardlayout')

@section('title', 'My Payments')

@section('content')
<div class="container-fluid px-0">

    {{-- 🔔 Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

     {{-- 🏠 Payment Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-gradient-warning text-white">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <i class="bi bi-cash-coin fs-1 mb-2 opacity-75"></i>
                    <h6 class="fw-semibold">Unpaid Rent ({{ $unpaidRentMonth }})</h6>
                    <h3 class="fw-bold mb-0">₱{{ number_format($unpaidRent, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-gradient-danger text-white"
                 style="cursor:pointer;" onclick="viewUtilityProof()">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <i class="bi bi-lightning-charge-fill fs-1 mb-2 opacity-75"></i>
                    <h6 class="fw-semibold">Unpaid Utilities ({{ $unpaidUtilitiesMonth }})</h6>
                    <h3 class="fw-bold mb-0">₱{{ number_format($unpaidUtilities, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-gradient-success text-white">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <i class="bi bi-wallet2 fs-1 mb-2 opacity-75"></i>
                    <h6 class="fw-semibold">Advance Payment</h6>
                    <h3 class="fw-bold mb-0">₱{{ number_format(auth()->user()->user_credit ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- 📜 Payment History --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center rounded-top-4">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Payment History</h5>
            <button class="btn btn-sm text-white px-3 py-2 rounded-3" style="background-color:#7e7eee;" data-bs-toggle="modal" data-bs-target="#makePaymentModal">
                <i class="bi bi-plus-lg"></i> Make Payment
            </button>
        </div>

        <div class="card-body">
            @if($payments->isEmpty())
                <p class="text-muted text-center py-4">No payments recorded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-borderless">
                        <thead class="table-light text-uppercase small text-muted">
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
                            <tr class="align-middle">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->pay_date ?? $payment->created_at)->format('M d, Y') }}</td>
                                <td>{{ $payment->payment_for ?? '-' }}</td>
                                <td>{{ ucfirst($payment->pay_method) }}</td>
                                <td class="fw-semibold text-success">₱{{ number_format($payment->pay_amount, 2) }}</td>
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
                        <option value="Other" data-balance="0">Pay in Advance</option>
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
                <button
                    class="btn btn-sm text-white"
                    data-bs-toggle="modal"
                    data-bs-target="#makePaymentModal"
                    style="background-color: #b8793e; border: none; border-radius: 8px; padding: 8px 16px;">
                    + Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>


{{-- 🔍 Utility Proof Modal --}}
<div class="modal fade" id="viewUtilityProofModal" tabindex="-1" aria-labelledby="viewUtilityProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">Proof of Utility Billing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="utilityProofImage" src="" alt="Utility Proof" class="img-fluid rounded-4 shadow-sm">
            </div>
        </div>
    </div>
</div>

{{-- 🧠 Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentFor = document.getElementById('payment_for');
    const payAmount = document.getElementById('pay_amount');
    const method = document.getElementById('payment_method');
    const accountField = document.getElementById('accountNumberField');

    method.addEventListener('change', () => {
        accountField.classList.toggle('d-none', !(method.value === 'GCash' || method.value === 'Bank Transfer'));
    });

    paymentFor.addEventListener('change', () => {
        const selected = paymentFor.options[paymentFor.selectedIndex];
        const balance = parseFloat(selected.dataset.balance || 0);
        payAmount.value = balance > 0 ? balance : '';
    });

    window.viewUtilityProof = function() {
        const proofPath = '{{ auth()->user()->proof_of_utility_billing ?? '' }}';
        const proofModal = new bootstrap.Modal(document.getElementById('viewUtilityProofModal'));
        const img = document.getElementById('utilityProofImage');
        if (proofPath) {
            img.src = '{{ asset('storage') }}' + '/' + proofPath;
            proofModal.show();
        } else {
            alert('No proof of utility billing available.');
        }
    };
});
</script>

{{-- 🌈 Styling --}}
{{-- 🌈 Custom Styling --}}
<style>
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffb347, #ffcc33);
    }
    .bg-gradient-danger {
        background: linear-gradient(135deg, #ff5f6d, #ffc371);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e, #38ef7d);
    }

    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .btn {
        transition: all 0.2s ease;
    }
    .btn:hover {
        opacity: .9;
    }
</style>

@endsection
