@extends('layouts.bootstrap')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1 fw-bold">Payment Details</h1>
        <p class="text-muted mb-0 small">
            {{ $vasRevenue->service->name ?? 'N/A' }} - 
            {{ $vasRevenue->payment_period_month && $vasRevenue->payment_period_year
                ? \Carbon\Carbon::create($vasRevenue->payment_period_year, $vasRevenue->payment_period_month)->format('F Y')
                : ($vasRevenue->period_label ?? 'N/A') }}
        </p>
    </div>
    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to Payments</span><span class="d-sm-none">Back</span>
    </a>
</div>

@if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-receipt text-primary"></i> Revenue Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Service:</strong> {{ $vasRevenue->service->name ?? 'N/A' }}
            </div>
            <div class="col-md-3">
                <strong>MNO:</strong> {{ $vasRevenue->mno->name ?? 'N/A' }}
            </div>
            <div class="col-md-3">
                <strong>Aggregator:</strong> {{ $vasRevenue->aggregator->name ?? 'N/A' }}
            </div>
            <div class="col-md-3">
                <strong>Bank:</strong> {{ $vasRevenue->bank->name ?? 'N/A' }}
            </div>
            <div class="col-md-3 mt-2">
                <strong>Amount Received:</strong> @naira($vasRevenue->gross_revenue_a ?? 0)
            </div>
            <div class="col-md-3 mt-2">
                <strong>Payment Date:</strong> {{ $vasRevenue->payment_date?->format('Y-m-d') ?? 'N/A' }}
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-list-check text-primary"></i> Payment Items
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Recipient</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vasRevenue->paymentItems as $item)
                        <tr data-item-id="{{ $item->id }}">
                            <td>{{ $item->recipient_name }}</td>
                            <td>
                                @if($item->payment_type === 'partner_share_dr') Partner Share (DR)
                                @elseif($item->payment_type === 'partner_share_aj') Partner Share (AJ)
                                @elseif($item->payment_type === 'partner_share_tj') Partner Share (TJ)
                                @elseif($item->payment_type === 'operational_expense') Operational Expense
                                @else {{ $item->payment_type }}
                                @endif
                            </td>
                            <td>@naira($item->amount)</td>
                            <td>
                                <select class="form-select form-select-sm payment-status" 
                                        data-item-id="{{ $item->id }}"
                                        style="min-width: 120px;">
                                    <option value="not_paid" {{ $item->status === 'not_paid' ? 'selected' : '' }}>Not Paid</option>
                                    <option value="paid" {{ $item->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" 
                                       class="form-control form-control-sm payment-comment" 
                                       data-item-id="{{ $item->id }}"
                                       value="{{ $item->comment ?? '' }}"
                                       placeholder="Add comment...">
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary save-payment-item" 
                                        data-item-id="{{ $item->id }}"
                                        style="display: none;">
                                    Save
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No payment items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.payment-status');
    const commentInputs = document.querySelectorAll('.payment-comment');
    const saveButtons = document.querySelectorAll('.save-payment-item');
    
    // Track changes
    const changedItems = new Set();
    
    function markChanged(itemId) {
        changedItems.add(itemId);
        const saveBtn = document.querySelector(`.save-payment-item[data-item-id="${itemId}"]`);
        if (saveBtn) {
            saveBtn.style.display = 'inline-block';
        }
    }
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            markChanged(this.dataset.itemId);
        });
    });
    
    commentInputs.forEach(input => {
        input.addEventListener('input', function() {
            markChanged(this.dataset.itemId);
        });
    });
    
    // Save functionality
    saveButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const itemId = this.dataset.itemId;
            const row = this.closest('tr');
            const statusSelect = row.querySelector('.payment-status');
            const commentInput = row.querySelector('.payment-comment');
            
            const data = {
                status: statusSelect.value,
                comment: commentInput.value,
                _token: document.querySelector('meta[name="csrf-token"]').content,
                _method: 'PATCH'
            };
            
            try {
                const response = await fetch(`/payments/items/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': data._token,
                        'X-HTTP-Method-Override': 'PATCH'
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    changedItems.delete(itemId);
                    this.style.display = 'none';
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = 'Payment item updated. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
                    setTimeout(() => alert.remove(), 3000);
                } else {
                    alert('Error updating payment item');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating payment item');
            }
        });
    });
});
</script>
@endpush
@endsection

