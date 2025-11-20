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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">VAS Revenues</h1>
            <a href="{{ route('revenue.create') }}" class="btn btn-primary mb-0">
                <i class="bi bi-plus-circle"></i> Add Revenue Entry
            </a>
        </div>

        @if(session('ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('ok') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>MNO</th>
                                <th>Aggregator</th>
                                <th>Gross Revenue</th>
                                <th>Aggregator %</th>
                                <th>Aggregator Net</th>
                                <th>Share Pool</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $item->service->name ?? 'N/A' }}</td>
                                    <td>{{ $item->mno->name ?? 'N/A' }}</td>
                                    <td>{{ $item->aggregator->name ?? 'N/A' }}</td>
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
                                    <td>
                                        <a href="{{ route('revenue.show', $item->id) }}" class="btn btn-sm btn-outline-primary">View</a>
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

