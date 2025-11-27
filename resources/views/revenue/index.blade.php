<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VAS Revenues - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold">VAS Revenues</h1>
                <p class="text-muted mb-0 small">Manage and track revenue entries</p>
            </div>
            <a href="{{ route('revenue.create') }}" class="btn btn-primary w-100 w-md-auto">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add Revenue Entry</span><span class="d-sm-none">Add Entry</span>
            </a>
        </div>

        @if(session('ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('ok') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-list-ul text-primary"></i> Revenue Entries
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>MNO</th>
                                <th>Aggregator</th>
                                <th>Bank</th>
                                <th>Gross Revenue</th>
                                <th>Aggregator %</th>
                                <th>Aggregator Net</th>
                                <th>Share Pool</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $item->service->name ?? 'N/A' }}</td>
                                    <td>{{ $item->mno->name ?? 'N/A' }}</td>
                                    <td>{{ $item->aggregator->name ?? 'N/A' }}</td>
                                    <td>{{ $item->bank->name ?? 'N/A' }}</td>
                                    <td>@naira($item->gross_revenue_a ?? 0)</td>
                                    <td>{{ number_format($item->aggregator_percentage, 4) }}%</td>
                                    <td>@naira($item->aggregator_net_x ?? 0)</td>
                                    <td>
                                        @if($item->partnerShareSummary)
                                            @naira($item->partnerShareSummary->rs_share_pool)
                                        @else
                                            <span class="text-muted">Not calculated</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('revenue.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No revenues found. <a href="{{ route('revenue.create') }}">Create one</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($items, 'links'))
                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

