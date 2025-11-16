@extends('layouts.bootstrap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4">VAS Revenue Details</h1>
    <a href="{{ route('revenue.index') }}" class="btn btn-outline-secondary">Back to List</a>
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

@php($summary = $vr->partnerShareSummary)

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Revenue Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Service:</dt>
                    <dd class="col-sm-8">{{ $vr->service->name ?? 'N/A' }} @if($vr->service && $vr->service->type)({{ $vr->service->type }})@endif</dd>

                    <dt class="col-sm-4">MNO:</dt>
                    <dd class="col-sm-8">{{ $vr->mno->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Aggregator:</dt>
                    <dd class="col-sm-8">{{ $vr->aggregator->name ?? 'N/A' }} @if($vr->aggregator && $vr->aggregator->short_code)({{ $vr->aggregator->short_code }})@endif</dd>

                    <dt class="col-sm-4">Payment Date:</dt>
                    <dd class="col-sm-8">{{ $vr->payment_date ? $vr->payment_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-4">Period Label:</dt>
                    <dd class="col-sm-8">{{ $vr->period_label ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Gross Revenue (A):</dt>
                    <dd class="col-sm-8"><strong>{{ number_format($vr->gross_revenue_a, 2) }}</strong></dd>

                    <dt class="col-sm-4">Aggregator Percentage (%A):</dt>
                    <dd class="col-sm-8"><strong>{{ number_format($vr->aggregator_percentage, 4) }}%</strong></dd>

                    <dt class="col-sm-4">Aggregator Net (X):</dt>
                    <dd class="col-sm-8"><strong>{{ number_format($vr->aggregator_net_x, 2) }}</strong></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Summary</h5>
            </div>
            <div class="card-body">
                @if($summary)
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Mandatory Expenses (ME):</dt>
                        <dd class="col-sm-6 text-end"><strong>{{ number_format($summary->mandatory_total_me, 2) }}</strong></dd>

                        <dt class="col-sm-6">RA (After Mandatory):</dt>
                        <dd class="col-sm-6 text-end"><strong>{{ number_format($summary->ra_after_mandatory, 2) }}</strong></dd>

                        <dt class="col-sm-6">Operational Expenses (OE):</dt>
                        <dd class="col-sm-6 text-end"><strong>{{ number_format($summary->operational_total_oe, 2) }}</strong></dd>

                        <dt class="col-sm-6">RS (Share Pool):</dt>
                        <dd class="col-sm-6 text-end"><strong class="text-primary">{{ number_format($summary->rs_share_pool, 2) }}</strong></dd>

                        <hr class="my-2">

                        <dt class="col-sm-6">DR Share (50%):</dt>
                        <dd class="col-sm-6 text-end"><strong>{{ number_format($summary->dr_share_50, 2) }}</strong></dd>

                        <dt class="col-sm-6">AJ Share (30%):</dt>
                        <dd class="col-sm-6 text-end"><strong>{{ number_format($summary->aj_share_30, 2) }}</strong></dd>

                        <dt class="col-sm-6">TJ Share (20%):</dt>
                        <dd class="col-sm-6 text-end"><strong>{{ number_format($summary->tj_share_20, 2) }}</strong></dd>

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

        <form method="POST" action="{{ route('revenue.recompute', $vr->id) }}">
            @csrf
            <button type="submit" class="btn {{ $summary ? 'btn-outline-primary' : 'btn-primary' }} w-100">
                {{ $summary ? 'Recalculate Summary' : 'Calculate Summary' }}
            </button>
        </form>
    </div>
</div>

@if($summary)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mandatory Expenses</h5>
                </div>
                <div class="card-body">
                    @if($vr->mandatoryExpenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vr->mandatoryExpenses as $expense)
                                        <tr>
                                            <td>{{ $expense->type->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($expense->final_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <th>Total ME:</th>
                                        <th>{{ number_format($summary->mandatory_total_me, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No mandatory expenses recorded.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Operational Expenses</h5>
                </div>
                <div class="card-body">
                    @if($vr->operationalExpenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Recipient</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vr->operationalExpenses as $expense)
                                        <tr>
                                            <td>{{ $expense->operationalCategory->name ?? 'N/A' }}</td>
                                            <td>{{ $expense->expenseRecipient->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($expense->final_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <th colspan="2">Total OE:</th>
                                        <th>{{ number_format($summary->operational_total_oe, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No operational expenses recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

