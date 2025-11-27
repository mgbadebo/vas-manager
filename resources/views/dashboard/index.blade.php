@extends('layouts.bootstrap')

@section('content')
  <div class="mb-4">
    <h1 class="h3 mb-1 fw-bold">Dashboard</h1>
    <p class="text-muted mb-0 small">Overview of your VAS revenue and operations</p>
  </div>

  {{-- 3-column responsive cards --}}
  <div class="row g-3">

    {{-- YTD Gross Revenue --}}
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('reports.revenue') }}" class="text-decoration-none text-reset">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="text-muted small fw-medium">YTD Gross Revenue</div>
              <i class="bi bi-graph-up-arrow text-primary fs-4"></i>
            </div>
            <div class="h3 mt-2 mb-0 fw-bold text-primary">@naira($A_ytd ?? 0)</div>
            <div class="mt-2 text-primary small">View revenue report →</div>
          </div>
        </div>
      </a>
    </div>

    {{-- YTD Revenue After Mandatory --}}
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('reports.revenue') }}#ra" class="text-decoration-none text-reset">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="text-muted small fw-medium">YTD Revenue After Mandatory</div>
              <i class="bi bi-cash-coin text-info fs-4"></i>
            </div>
            <div class="h3 mt-2 mb-0 fw-bold text-info">@naira($RA_ytd ?? 0)</div>
            <div class="mt-2 text-primary small">See breakdown →</div>
          </div>
        </div>
      </a>
    </div>

    {{-- YTD Revenue Share Pool --}}
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('reports.revenue') }}#rs" class="text-decoration-none text-reset">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="text-muted small fw-medium">YTD Revenue Share Pool</div>
              <i class="bi bi-piggy-bank text-success fs-4"></i>
            </div>
            <div class="h3 mt-2 mb-0 fw-bold text-success">@naira($RS_ytd ?? 0)</div>
            <div class="mt-2 text-primary small">See breakdown →</div>
          </div>
        </div>
      </a>
    </div>

    {{-- Entities --}}
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Entities</div>
          <div class="mt-2">Services: <strong>{{ $countServices }}</strong></div>
          <div>MNOs: <strong>{{ $countMNOs }}</strong></div>
          <div>Aggregators: <strong>{{ $countAggregators }}</strong></div>
          <div class="mt-2">
            <a href="{{ route('reports.services') }}" class="small me-3">Services →</a>
            <a href="{{ route('reports.mnos') }}" class="small me-3">MNOs →</a>
            <a href="{{ route('reports.aggregators') }}" class="small">Aggregators →</a>
          </div>
        </div>
      </div>
    </div>

    {{-- Last revenue --}}
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small mb-1">Last Revenue</div>
          @if($lastRevenue)
            <div class="small text-muted">
              <strong>Service:</strong> {{ optional($lastRevenue->service)->name }} ·
              <strong>MNO:</strong> {{ optional($lastRevenue->mno)->name }} ·
              <strong>Aggregator:</strong> {{ optional($lastRevenue->aggregator)->name }}
            </div>
            <div class="mt-2">
              <strong>Gross Revenue:</strong> @naira($lastRevenue->gross_revenue_a ?? 0)<br>
              <strong>Aggregator %:</strong> {{ number_format($lastRevenue->aggregator_percentage,2) }}%<br>
              <strong>Aggregator Net:</strong> @naira($lastRevenue->aggregator_net_x ?? 0)
            </div>
            @if($lastRevenue->partnerShareSummary)
              <div class="mt-2">
                <strong>Revenue Share Pool:</strong> @naira(optional($lastRevenue->partnerShareSummary)->rs_share_pool ?? 0)
                <a href="{{ route('revenue.show', $lastRevenue->id) }}" class="ms-3 small">Open record →</a>
              </div>
            @endif
          @else
            <div class="text-muted">No revenue records yet.</div>
          @endif
        </div>
      </div>
    </div>

    {{-- RS by Month chart (full width row) --}}
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">YTD Revenue Share Pool by Month</h5>
          <canvas id="rsByMonthChart" height="110"></canvas>
        </div>
      </div>
    </div>

  </div>
@endsection

@push('scripts')
<script>
  const ctx = document.getElementById('rsByMonthChart').getContext('2d');
  const labels = {!! json_encode($labels) !!};
  const data   = {!! json_encode($rsSeries) !!};

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{ label: 'Revenue Share Pool', data: data, tension: 0.25 }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
</script>
@endpush

