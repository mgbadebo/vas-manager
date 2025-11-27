@extends('layouts.bootstrap')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1 fw-bold">Payments Dashboard</h1>
        <p class="text-muted mb-0 small">Track and manage payment obligations</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 w-md-auto">
        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to Dashboard</span><span class="d-sm-none">Back</span>
    </a>
</div>

@if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Section 1: Regular Payments -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-credit-card text-primary"></i> Regular Payments
        </h5>
        <small class="text-muted">Payments to shareholders and recipients (paid as soon as possible)</small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Payment Period</th>
                        <th>Aggregator Name</th>
                        <th>Amount Received</th>
                        <th>MNO</th>
                        <th>Service</th>
                        <th>Payment Date</th>
                        <th>Bank</th>
                        <th>Total Expenses</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenuePayments as $payment)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('payments.show', $payment['id']) }}'" class="hover-row">
                            <td><span class="badge bg-light text-dark">{{ $payment['payment_period'] }}</span></td>
                            <td>{{ $payment['aggregator_name'] }}</td>
                            <td><strong class="text-primary">@naira($payment['amount_received'])</strong></td>
                            <td>{{ $payment['mno'] }}</td>
                            <td>{{ $payment['service'] }}</td>
                            <td><small class="text-muted">{{ $payment['payment_date'] }}</small></td>
                            <td>{{ $payment['bank'] }}</td>
                            <td>@naira($payment['total_expenses'])</td>
                            <td><strong class="{{ $payment['balance'] >= 0 ? 'text-success' : 'text-danger' }}">@naira($payment['balance'])</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No revenue entries with calculated payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Section 2: Mandatory Expenses (Annual Accumulations) -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-calendar-year text-warning"></i> Mandatory Expenses (Annual Accumulations)
        </h5>
        <small class="text-muted">Accumulated and paid annually</small>
    </div>
    <div class="card-body">
        @forelse($mandatoryExpenses as $year => $items)
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Year: {{ $year }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>Payment Period</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Bank</th>
                                <th>Moved Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr data-accumulation-id="{{ $item->id }}">
                                    <td>{{ $item->service->name ?? 'N/A' }}</td>
                                    <td>{{ $year }}</td>
                                    <td>{{ $item->mandatoryExpenseType->name ?? 'N/A' }}</td>
                                    <td>@naira($item->total_amount)</td>
                                    <td>
                                        <select class="form-select form-select-sm mandatory-bank" 
                                                data-accumulation-id="{{ $item->id }}"
                                                data-original-value="{{ $item->bank_id ?? '' }}"
                                                style="min-width: 150px;"
                                                {{ $item->bank_id || $item->moved_to_bank_date ? 'disabled' : '' }}>
                                            <option value="">-- select --</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ $item->bank_id == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" 
                                               class="form-control form-control-sm mandatory-moved-date" 
                                               data-accumulation-id="{{ $item->id }}"
                                               data-original-value="{{ $item->moved_to_bank_date?->format('Y-m-d') ?? '' }}"
                                               value="{{ $item->moved_to_bank_date?->format('Y-m-d') ?? '' }}"
                                               {{ $item->bank_id || $item->moved_to_bank_date ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary save-mandatory-accumulation" 
                                                data-accumulation-id="{{ $item->id }}"
                                                style="display: none;"
                                                title="Click to save changes">
                                            <i class="bi bi-save"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-mandatory-accumulation" 
                                                data-accumulation-id="{{ $item->id }}"
                                                style="{{ $item->bank_id || $item->moved_to_bank_date ? 'display: inline-block;' : 'display: none;' }}"
                                                title="Click to edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="table-info fw-bold">
                                <td colspan="3" class="text-end">Total for {{ $year }}:</td>
                                <td>@naira($items->sum('total_amount'))</td>
                                <td colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No mandatory expense accumulations found.</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bankSelects = document.querySelectorAll('.mandatory-bank');
    const movedDateInputs = document.querySelectorAll('.mandatory-moved-date');
    const saveButtons = document.querySelectorAll('.save-mandatory-accumulation');
    
    // Track changes
    const changedAccumulations = new Set();
    
    function enableEditMode(accumulationId) {
        const row = document.querySelector(`tr[data-accumulation-id="${accumulationId}"]`);
        if (!row) return;
        
        const bankSelect = row.querySelector('.mandatory-bank');
        const movedDateInput = row.querySelector('.mandatory-moved-date');
        const saveBtn = row.querySelector('.save-mandatory-accumulation');
        const editBtn = row.querySelector('.edit-mandatory-accumulation');
        
        if (bankSelect) bankSelect.disabled = false;
        if (movedDateInput) movedDateInput.disabled = false;
        if (saveBtn) saveBtn.style.display = 'none';
        if (editBtn) editBtn.style.display = 'none';
    }
    
    function disableEditMode(accumulationId) {
        const row = document.querySelector(`tr[data-accumulation-id="${accumulationId}"]`);
        if (!row) return;
        
        const bankSelect = row.querySelector('.mandatory-bank');
        const movedDateInput = row.querySelector('.mandatory-moved-date');
        const saveBtn = row.querySelector('.save-mandatory-accumulation');
        const editBtn = row.querySelector('.edit-mandatory-accumulation');
        
        if (bankSelect) {
            bankSelect.disabled = true;
            bankSelect.setAttribute('data-original-value', bankSelect.value);
        }
        if (movedDateInput) {
            movedDateInput.disabled = true;
            movedDateInput.setAttribute('data-original-value', movedDateInput.value);
        }
        if (saveBtn) saveBtn.style.display = 'none';
        if (editBtn) editBtn.style.display = 'inline-block';
        
        changedAccumulations.delete(accumulationId);
    }
    
    function markChanged(accumulationId) {
        changedAccumulations.add(accumulationId);
        const row = document.querySelector(`tr[data-accumulation-id="${accumulationId}"]`);
        if (!row) return;
        
        const saveBtn = row.querySelector('.save-mandatory-accumulation');
        const editBtn = row.querySelector('.edit-mandatory-accumulation');
        
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (editBtn) editBtn.style.display = 'none';
    }
    
    bankSelects.forEach(select => {
        const accumulationId = select.getAttribute('data-accumulation-id');
        select.addEventListener('change', function() {
            if (!this.disabled) {
                markChanged(accumulationId);
            }
        });
    });
    
    movedDateInputs.forEach(input => {
        const accumulationId = input.getAttribute('data-accumulation-id');
        input.addEventListener('change', function() {
            if (!this.disabled) {
                markChanged(accumulationId);
            }
        });
        input.addEventListener('input', function() {
            if (!this.disabled) {
                markChanged(accumulationId);
            }
        });
    });
    
    // Edit button functionality
    const editButtons = document.querySelectorAll('.edit-mandatory-accumulation');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const accumulationId = this.getAttribute('data-accumulation-id');
            enableEditMode(accumulationId);
        });
    });
    
    // Save functionality
    saveButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const accumulationId = this.dataset.accumulationId;
            const row = this.closest('tr');
            const bankSelect = row.querySelector('.mandatory-bank');
            const movedDateInput = row.querySelector('.mandatory-moved-date');
            
            const data = {
                bank_id: bankSelect.value || null,
                moved_to_bank_date: movedDateInput.value || null,
                _token: document.querySelector('meta[name="csrf-token"]').content,
                _method: 'PATCH'
            };
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || data._token;
                
                // Use FormData for method spoofing (Laravel supports this better)
                const formData = new FormData();
                formData.append('_method', 'PATCH');
                formData.append('_token', csrfToken);
                if (bankSelect.value) {
                    formData.append('bank_id', bankSelect.value);
                }
                if (movedDateInput.value) {
                    formData.append('moved_to_bank_date', movedDateInput.value);
                }
                
                const response = await fetch(`/payments/mandatory/${accumulationId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                let result;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    result = { success: response.ok, message: 'Updated successfully' };
                }
                
                if (response.ok && result.success !== false) {
                    // Disable fields and show edit button
                    disableEditMode(accumulationId);
                    
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = (result.message || 'Mandatory expense accumulation updated.') + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    const container = document.querySelector('.container');
                    if (container) {
                        container.insertBefore(alert, container.firstChild);
                        setTimeout(() => alert.remove(), 3000);
                    }
                } else {
                    const errorMsg = result.message || result.error || 'Error updating mandatory expense accumulation';
                    alert(errorMsg);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating mandatory expense accumulation: ' + error.message);
            }
        });
    });
});
</script>
@endpush
@endsection

