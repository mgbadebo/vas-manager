@extends('layouts.bootstrap')

@section('content')
@php
    $summary = $vr->partnerShareSummary;
    $mandatoryErrors = $errors->getBag('mandatoryExpense');
    $operationalErrors = $errors->getBag('operationalExpense');
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1 fw-bold">VAS Revenue Details</h1>
        <p class="text-muted mb-0 small">View and manage revenue entry information</p>
    </div>
    <a href="{{ route('revenue.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

@if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-receipt text-primary"></i> Revenue Information
                </h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Service:</dt>
                    <dd class="col-sm-8 mb-2">{{ $vr->service->name ?? 'N/A' }} @if($vr->service && $vr->service->serviceType)<span class="badge bg-primary-subtle text-primary ms-2">{{ $vr->service->serviceType->name }}</span>@endif</dd>

                    <dt class="col-sm-4 text-muted fw-normal">MNO:</dt>
                    <dd class="col-sm-8 mb-2">{{ $vr->mno->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Aggregator:</dt>
                    <dd class="col-sm-8 mb-2">{{ $vr->aggregator->name ?? 'N/A' }} @if($vr->aggregator && $vr->aggregator->short_code)<span class="badge bg-secondary-subtle text-secondary ms-2">{{ $vr->aggregator->short_code }}</span>@endif</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Payment Date:</dt>
                    <dd class="col-sm-8 mb-2">{{ $vr->payment_date ? $vr->payment_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Payment Period:</dt>
                    <dd class="col-sm-8 mb-2">
                        @if($vr->payment_period_month && $vr->payment_period_year)
                            {{ \Carbon\Carbon::create($vr->payment_period_year, $vr->payment_period_month)->format('F Y') }}
                        @else
                            N/A
                        @endif
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Bank:</dt>
                    <dd class="col-sm-8 mb-2">{{ $vr->bank->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Period Label:</dt>
                    <dd class="col-sm-8 mb-2">{{ $vr->period_label ?? 'N/A' }}</dd>

                    <hr class="my-2">

                    <dt class="col-sm-4 text-muted fw-normal">Gross Revenue:</dt>
                    <dd class="col-sm-8 mb-2"><strong class="text-primary fs-5">@naira($vr->gross_revenue_a ?? 0)</strong></dd>

                    <dt class="col-sm-4 text-muted fw-normal">Aggregator Percentage:</dt>
                    <dd class="col-sm-8 mb-2"><strong>{{ number_format($vr->aggregator_percentage, 4) }}%</strong></dd>

                    <dt class="col-sm-4 text-muted fw-normal">Aggregator Net:</dt>
                    <dd class="col-sm-8 mb-2"><strong class="text-success">@naira($vr->aggregator_net_x ?? 0)</strong></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-calculator text-success"></i> Summary
                </h5>
            </div>
            <div class="card-body">
                @if($summary)
                    <dl class="row mb-0">
                        <dt class="col-sm-6 text-muted fw-normal small">Mandatory Expenses (ME):</dt>
                        <dd class="col-sm-6 text-end mb-2"><strong>@naira($summary->mandatory_total_me ?? 0)</strong></dd>

                        <dt class="col-sm-6 text-muted fw-normal small">Revenue After Mandatory:</dt>
                        <dd class="col-sm-6 text-end mb-2"><strong class="text-info">@naira($summary->ra_after_mandatory ?? 0)</strong></dd>

                        <dt class="col-sm-6 text-muted fw-normal small">Operational Expenses:</dt>
                        <dd class="col-sm-6 text-end mb-2"><strong>@naira($summary->operational_total_oe ?? 0)</strong></dd>

                        <dt class="col-sm-6 text-muted fw-normal small">Revenue Share Pool:</dt>
                        <dd class="col-sm-6 text-end mb-2"><strong class="text-primary fs-5">@naira($summary->rs_share_pool ?? 0)</strong></dd>

                        <hr class="my-3">

                        <dt class="col-sm-6 text-muted fw-normal small">DR Share:</dt>
                        <dd class="col-sm-6 text-end mb-2"><strong class="share-amount text-success" data-amount="{{ $summary->dr_share_50 ?? 0 }}">*****</strong></dd>

                        <dt class="col-sm-6 text-muted fw-normal small">AJ Share:</dt>
                        <dd class="col-sm-6 text-end mb-2"><strong class="share-amount text-success" data-amount="{{ $summary->aj_share_30 ?? 0 }}">*****</strong></dd>

                        <dt class="col-sm-6 text-muted fw-normal small">TJ Share:</dt>
                        <dd class="col-sm-6 text-end mb-2"><strong class="share-amount text-success" data-amount="{{ $summary->tj_share_20 ?? 0 }}">*****</strong></dd>

                        @if($summary->computed_on)
                            <dt class="col-sm-12 mt-2">
                                <small class="text-muted">Computed on: {{ $summary->computed_on->format('Y-m-d H:i:s') }}</small>
                            </dt>
                        @endif
                    </dl>
                @else
                    <div class="alert alert-warning mb-0">
                        Summary not calculated yet. Please run the calculation.
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-2">
            <form method="POST" action="{{ route('revenue.recompute', $vr->id) }}" class="flex-fill">
                @csrf
                <button type="submit" class="btn {{ $summary ? 'btn-outline-primary' : 'btn-primary' }} w-100">
                    <i class="bi bi-arrow-clockwise"></i> <span class="d-none d-sm-inline">{{ $summary ? 'Recalculate Summary' : 'Calculate Summary' }}</span><span class="d-sm-none">{{ $summary ? 'Recalculate' : 'Calculate' }}</span>
                </button>
            </form>
            
            @if($summary)
                <button type="button" class="btn btn-outline-secondary flex-fill" id="toggleShareButton">
                    <i class="bi bi-eye"></i> <span class="d-none d-sm-inline">Show Share</span><span class="d-sm-none">Show</span>
                </button>
            @endif
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-exclamation-triangle text-warning"></i> Mandatory Expenses
                </h5>
            </div>
            <div class="card-body">
                <form class="row g-3 mb-3" method="POST" action="{{ route('mandatory-expenses.store', $vr) }}">
                    @csrf
                    <div class="col-12 col-md-4">
                        <label class="form-label">Type</label>
                        <select name="mandatory_expense_type_id" class="form-select @error('mandatory_expense_type_id','mandatoryExpense') is-invalid @enderror">
                            <option value="">-- select --</option>
                            @foreach($mandatoryTypes as $type)
                                <option value="{{ $type->id }}" @selected($mandatoryErrors->any() && (int) old('mandatory_expense_type_id') === $type->id)>{{ $type->name }} ({{ $type->rule_type }})</option>
                            @endforeach
                        </select>
                        @error('mandatory_expense_type_id','mandatoryExpense')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Percentage (%)</label>
                        <input type="number" step="0.0001" id="mandatory_percentage" name="percentage" data-toggle-pair="mandatory_fixed_amount" class="form-control @error('percentage','mandatoryExpense') is-invalid @enderror"
                            value="{{ $mandatoryErrors->any() ? old('percentage') : '' }}">
                        @error('percentage','mandatoryExpense')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Fixed Amount</label>
                        <input type="number" step="0.01" id="mandatory_fixed_amount" name="fixed_amount" data-toggle-pair="mandatory_percentage" class="form-control @error('fixed_amount','mandatoryExpense') is-invalid @enderror"
                            value="{{ $mandatoryErrors->any() ? old('fixed_amount') : '' }}">
                        @error('fixed_amount','mandatoryExpense')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Expense
                        </button>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Provide either percentage or fixed amount. Percentages use the rule defined on the selected type.</small>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Percentage</th>
                                <th>Fixed Amount</th>
                                <th>Value</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vr->mandatoryExpenses as $expense)
                                @php
                                    $value = 0;
                                    if ($expense->fixed_amount !== null) {
                                        $value = $expense->fixed_amount;
                                    } elseif ($expense->type && $expense->percentage !== null) {
                                        $base = 0;
                                        $ruleType = $expense->type->rule_type;
                                        
                                        if ($ruleType === 'Percent_of_X') {
                                            $base = $vr->aggregator_net_x ?? 0;
                                        } elseif ($ruleType === 'Percent_of_RA' && $summary) {
                                            $base = $summary->ra_after_mandatory ?? 0;
                                        } elseif ($ruleType === 'Percent_of_RS' && $summary) {
                                            $base = $summary->rs_share_pool ?? 0;
                                        }
                                        
                                        $value = $base * ($expense->percentage / 100);
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $expense->type->name ?? 'N/A' }}</td>
                                    <td>{{ $expense->percentage ? number_format($expense->percentage, 4) . '%' : '—' }}</td>
                                    <td>
                                        @if($expense->fixed_amount)
                                            @naira($expense->fixed_amount)
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>@naira($value)</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('mandatory-expenses.destroy', [$vr, $expense]) }}" onsubmit="return confirm('Remove this expense?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted text-center">No mandatory expenses recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($summary)
                            <tfoot>
                                <tr class="table-secondary">
                                    <th colspan="3">Total ME (Summary)</th>
                                    <th>@naira($summary->mandatory_total_me ?? 0)</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Operational Expenses</h5>
            </div>
            <div class="card-body">
                <form class="row g-3 mb-3" method="POST" action="{{ route('operational-expenses.store', $vr) }}">
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label">Category</label>
                        <select name="operational_category_id" class="form-select @error('operational_category_id','operationalExpense') is-invalid @enderror">
                            <option value="">-- select --</option>
                            @foreach($operationalCategories as $category)
                                <option value="{{ $category->id }}" @selected($operationalErrors->any() && (int) old('operational_category_id') === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('operational_category_id','operationalExpense')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Recipient</label>
                        <div class="input-group">
                            <select name="expense_recipient_id" id="expense_recipient_id" class="form-select @error('expense_recipient_id','operationalExpense') is-invalid @enderror">
                                <option value="">-- select --</option>
                                <option value="misc">Misc</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addRecipientModal">
                                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add New</span>
                            </button>
                        </div>
                        @error('expense_recipient_id','operationalExpense')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Percentage (%)</label>
                        <input type="number" step="0.0001" id="operational_percentage" name="percentage" data-toggle-pair="operational_fixed_amount" class="form-control @error('percentage','operationalExpense') is-invalid @enderror"
                            value="{{ $operationalErrors->any() ? old('percentage') : '' }}">
                        @error('percentage','operationalExpense')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Fixed Amount</label>
                        <input type="number" step="0.01" id="operational_fixed_amount" name="fixed_amount" data-toggle-pair="operational_percentage" class="form-control @error('fixed_amount','operationalExpense') is-invalid @enderror"
                            value="{{ $operationalErrors->any() ? old('fixed_amount') : '' }}">
                        @error('fixed_amount','operationalExpense')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Expense
                        </button>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Provide either percentage or fixed amount. Percentages use Revenue After Mandatory as the base.</small>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Recipient</th>
                                <th>Percentage</th>
                                <th>Fixed Amount</th>
                                <th>Value</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vr->operationalExpenses as $expense)
                                @php
                                    $value = 0;
                                    if ($expense->fixed_amount !== null) {
                                        $value = $expense->fixed_amount;
                                    } elseif ($expense->percentage !== null && $summary) {
                                        $base = $summary->ra_after_mandatory ?? 0;
                                        $value = $base * ($expense->percentage / 100);
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $expense->operationalCategory->name ?? 'N/A' }}</td>
                                    <td>{{ $expense->expenseRecipient->name ?? 'Misc' }}</td>
                                    <td>{{ $expense->percentage ? number_format($expense->percentage, 4) . '%' : '—' }}</td>
                                    <td>
                                        @if($expense->fixed_amount)
                                            @naira($expense->fixed_amount)
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>@naira($value)</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('operational-expenses.destroy', [$vr, $expense]) }}" onsubmit="return confirm('Remove this expense?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted text-center">No operational expenses recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($summary)
                            <tfoot>
                                <tr class="table-secondary">
                                    <th colspan="4">Total OE (Summary)</th>
                                    <th>@naira($summary->operational_total_oe ?? 0)</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Recipient Modal -->
<div class="modal fade" id="addRecipientModal" tabindex="-1" aria-labelledby="addRecipientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRecipientModalLabel">Add New Recipient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addRecipientForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipient_category" class="form-label">Category</label>
                        <select id="recipient_category" class="form-select" required>
                            <option value="">-- select --</option>
                            @foreach($operationalCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">Recipient Name / Company</label>
                        <input type="text" id="recipient_name" class="form-control" required placeholder="Enter recipient name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Recipient</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const categorySelect = document.querySelector('select[name="operational_category_id"]');
    const recipientSelect = document.getElementById('expense_recipient_id');
    const addRecipientForm = document.getElementById('addRecipientForm');
    const recipientModal = new bootstrap.Modal(document.getElementById('addRecipientModal'));

    // Store all recipients by category
    const recipientsByCategory = @json($expenseRecipients->groupBy('operational_category_id')->map(function($group) {
        return $group->map(function($r) {
            return ['id' => $r->id, 'name' => $r->name];
        })->values();
    }));

    // Load recipients when category changes
    function loadRecipientsForCategory(categoryId) {
        // Clear existing options except "Misc"
        recipientSelect.innerHTML = '<option value="">-- select --</option><option value="misc">Misc</option>';
        
        if (categoryId) {
            // Handle both string and number keys
            const categoryKey = String(categoryId);
            if (recipientsByCategory[categoryKey]) {
                recipientsByCategory[categoryKey].forEach(function(recipient) {
                    const option = document.createElement('option');
                    option.value = recipient.id;
                    option.textContent = recipient.name;
                    recipientSelect.appendChild(option);
                });
            }
        }
    }

    // Initial load if category is already selected
    if (categorySelect.value) {
        loadRecipientsForCategory(categorySelect.value);
    }

    // Listen for category changes
    categorySelect.addEventListener('change', function() {
        loadRecipientsForCategory(this.value);
        
        // Update modal category if open
        const modalCategory = document.getElementById('recipient_category');
        if (modalCategory && this.value) {
            modalCategory.value = this.value;
        }
    });

    // Handle form submission for adding new recipient
    addRecipientForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const categoryId = document.getElementById('recipient_category').value;
        const name = document.getElementById('recipient_name').value.trim();

        if (!categoryId || !name) {
            alert('Please fill in both category and recipient name.');
            return;
        }

        try {
            const response = await fetch('{{ route("expense-recipients.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    name: name,
                    operational_category_id: categoryId
                })
            });

            const data = await response.json();

            if (data.success) {
                // Add to local storage
                const categoryKey = String(categoryId);
                if (!recipientsByCategory[categoryKey]) {
                    recipientsByCategory[categoryKey] = [];
                }
                recipientsByCategory[categoryKey].push({
                    id: data.recipient.id,
                    name: data.recipient.name
                });

                // Add to dropdown if this category is selected
                if (String(categorySelect.value) === String(categoryId)) {
                    const option = document.createElement('option');
                    option.value = data.recipient.id;
                    option.textContent = data.recipient.name;
                    option.selected = true;
                    recipientSelect.appendChild(option);
                }

                // Reset form and close modal
                addRecipientForm.reset();
                recipientModal.hide();
            } else {
                alert('Error adding recipient. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error adding recipient. Please try again.');
        }
    });

    // Set modal category to match form category when opened
    document.getElementById('addRecipientModal').addEventListener('show.bs.modal', function() {
        const currentCategory = categorySelect.value;
        if (currentCategory) {
            document.getElementById('recipient_category').value = currentCategory;
        }
    });

    // Handle "misc" option - convert to null before form submission
    const operationalExpenseForm = document.querySelector('form[action="{{ route("operational-expenses.store", $vr) }}"]');
    if (operationalExpenseForm) {
        operationalExpenseForm.addEventListener('submit', function(e) {
            if (recipientSelect.value === 'misc') {
                recipientSelect.value = '';
            }
        });
    }

    // Mutually exclusive percentage/fixed fields
    function setupMutualExclusion(firstId, secondId) {
        const first = document.getElementById(firstId);
        const second = document.getElementById(secondId);
        if (!first || !second) {
            return;
        }

        const sync = (source, target) => {
            if (source.value.trim() !== '') {
                target.value = '';
            }
            target.disabled = source.value.trim() !== '';
            target.classList.toggle('bg-light', target.disabled);
        };

        first.addEventListener('input', () => sync(first, second));
        second.addEventListener('input', () => sync(second, first));

        sync(first, second);
        sync(second, first);
    }

    setupMutualExclusion('mandatory_percentage', 'mandatory_fixed_amount');
    setupMutualExclusion('operational_percentage', 'operational_fixed_amount');

    // Toggle share visibility
    const toggleShareButton = document.getElementById('toggleShareButton');
    if (toggleShareButton) {
        const shareAmounts = document.querySelectorAll('.share-amount');
        let sharesHidden = true; // Start with shares hidden

        // Store original formatted amounts on page load
        shareAmounts.forEach(function(element) {
            const amount = parseFloat(element.dataset.amount) || 0;
            // Format as Naira (matching the @naira directive format)
            const formatted = '₦' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            element.dataset.originalContent = formatted;
        });

        toggleShareButton.addEventListener('click', function() {
            sharesHidden = !sharesHidden;
            
            shareAmounts.forEach(function(element) {
                if (sharesHidden) {
                    // Show asterisks
                    element.innerHTML = '*****';
                } else {
                    // Restore original content
                    element.innerHTML = element.dataset.originalContent;
                }
            });

            // Toggle button text
            toggleShareButton.textContent = sharesHidden ? 'Show Share' : 'Hide Share';
        });
    }
})();
</script>
@endpush
@endsection

