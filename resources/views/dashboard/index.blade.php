@extends('layouts.bootstrap')

@section('content')
  <h1 class="h3 mb-4">Dashboard</h1>

  {{-- 3-column responsive cards --}}
  <div class="row g-3">

    {{-- YTD Gross (A) --}}
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('reports.revenue') }}" class="text-decoration-none text-reset">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="text-muted small">YTD Gross (A)</div>
            <div class="h3 mt-2 mb-0">{{ number_format($A_ytd ?? 0, 2) }}</div>
            <div class="mt-2 text-primary small">View revenue report →</div>
          </div>
        </div>
      </a>
    </div>

    {{-- YTD RA --}}
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('reports.revenue') }}#ra" class="text-decoration-none text-reset">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="text-muted small">YTD RA (After Mandatory)</div>
            <div class="h3 mt-2 mb-0">{{ number_format($RA_ytd ?? 0, 2) }}</div>
            <div class="mt-2 text-primary small">See breakdown →</div>
          </div>
        </div>
      </a>
    </div>

    {{-- YTD RS --}}
    <div class="col-12 col-md-6 col-lg-4">
      <a href="{{ route('reports.revenue') }}#rs" class="text-decoration-none text-reset">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="text-muted small">YTD RS (Share Pool)</div>
            <div class="h3 mt-2 mb-0">{{ number_format($RS_ytd ?? 0, 2) }}</div>
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
              A: <strong>{{ number_format($lastRevenue->gross_revenue_a,2) }}</strong>,
              %A: <strong>{{ number_format($lastRevenue->aggregator_percentage,2) }}%</strong>,
              X: <strong>{{ number_format($lastRevenue->aggregator_net_x,2) }}</strong>
            </div>
            @if($lastRevenue->partnerShareSummary)
              <div class="mt-2">
                RS: <strong>{{ number_format($lastRevenue->partnerShareSummary->rs_share_pool,2) }}</strong>
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
          <h5 class="card-title mb-3">YTD RS by Month</h5>
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
      datasets: [{ label: 'RS (Share Pool)', data: data, tension: 0.25 }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
</script>
@endpush

