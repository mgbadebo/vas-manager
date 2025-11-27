@extends('layouts.bootstrap')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1 fw-bold">Revenue Report</h1>
        <p class="text-muted mb-0 small">{{ $start->format('Y-m-d') }} → {{ $end->format('Y-m-d') }}</p>
    </div>
    <a href="{{ route('revenue.create') }}" class="btn btn-primary w-100 w-md-auto">
        <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add Revenue Entry</span><span class="d-sm-none">Add Entry</span>
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-diagram-3 text-primary"></i> By Aggregator
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Aggregator</th>
                        <th class="text-end">Gross Revenue</th>
                        <th class="text-end">After Mandatory</th>
                        <th class="text-end">Share Pool</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byAggregator as $r)
                        <tr>
                            <td><strong>{{ $r->aggregator }}</strong></td>
                            <td class="text-end"><strong class="text-primary">@naira($r->A ?? 0)</strong></td>
                            <td class="text-end"><strong class="text-info">@naira($r->RA ?? 0)</strong></td>
                            <td class="text-end"><strong class="text-success">@naira($r->RS ?? 0)</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-phone text-success"></i> By MNO
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>MNO</th>
                        <th class="text-end">Gross Revenue</th>
                        <th class="text-end">After Mandatory</th>
                        <th class="text-end">Share Pool</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byMNO as $r)
                        <tr>
                            <td><strong>{{ $r->mno }}</strong></td>
                            <td class="text-end"><strong class="text-primary">@naira($r->A ?? 0)</strong></td>
                            <td class="text-end"><strong class="text-info">@naira($r->RA ?? 0)</strong></td>
                            <td class="text-end"><strong class="text-success">@naira($r->RS ?? 0)</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-box-seam text-info"></i> By Service
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Service</th>
                        <th class="text-end">Gross Revenue</th>
                        <th class="text-end">After Mandatory</th>
                        <th class="text-end">Share Pool</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byService as $r)
                        <tr>
                            <td><strong>{{ $r->service }}</strong></td>
                            <td class="text-end"><strong class="text-primary">@naira($r->A ?? 0)</strong></td>
                            <td class="text-end"><strong class="text-info">@naira($r->RA ?? 0)</strong></td>
                            <td class="text-end"><strong class="text-success">@naira($r->RS ?? 0)</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-list-ul text-warning"></i> Revenue Lines (YTD)
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>MNO</th>
                        <th>Aggregator</th>
                        <th class="text-end">Gross Revenue</th>
                        <th class="text-end">Aggregator %</th>
                        <th class="text-end">Aggregator Net</th>
                        <th class="text-end">Share Pool</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $it)
                        <tr>
                            <td><small class="text-muted">{{ $it->payment_date }}</small></td>
                            <td>{{ optional($it->service)->name }}</td>
                            <td>{{ optional($it->mno)->name }}</td>
                            <td>{{ optional($it->aggregator)->name }}</td>
                            <td class="text-end"><strong class="text-primary">@naira($it->gross_revenue_a ?? 0)</strong></td>
                            <td class="text-end">{{ number_format($it->aggregator_percentage,2) }}%</td>
                            <td class="text-end">@naira($it->aggregator_net_x ?? 0)</td>
                            <td class="text-end">
                                @if(optional($it->partnerShareSummary)->rs_share_pool)
                                    <strong class="text-success">@naira($it->partnerShareSummary->rs_share_pool)</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('revenue.show',$it->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(method_exists($list, 'links'))
            <div class="card-footer bg-white border-top">
                {{ $list->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
