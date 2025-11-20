@extends('layouts.bootstrap')

@section('content')
<h1 class="h4 mb-3">Revenue Report ({{ $start->format('Y-m-d') }} → {{ $end->format('Y-m-d') }})</h1>

<a href="{{ route('revenue.create') }}" class="btn btn-primary mb-4">
    <i class="bi bi-plus-circle"></i> Add Revenue Entry
</a>

<h5 class="mt-4">By Aggregator</h5>
<table class="table table-sm table-bordered">
  <thead><tr><th>Aggregator</th><th>Gross Revenue</th><th>After Mandatory</th><th>Share Pool</th></tr></thead>
  <tbody>
  @foreach($byAggregator as $r)
    <tr>
      <td>{{ $r->aggregator }}</td>
      <td>@naira($r->A ?? 0)</td>
      <td>@naira($r->RA ?? 0)</td>
      <td>@naira($r->RS ?? 0)</td>
    </tr>
  @endforeach
  </tbody>
</table>

<h5 class="mt-4">By MNO</h5>
<table class="table table-sm table-bordered">
  <thead><tr><th>MNO</th><th>Gross Revenue</th><th>After Mandatory</th><th>Share Pool</th></tr></thead>
  <tbody>
  @foreach($byMNO as $r)
    <tr>
      <td>{{ $r->mno }}</td>
      <td>@naira($r->A ?? 0)</td>
      <td>@naira($r->RA ?? 0)</td>
      <td>@naira($r->RS ?? 0)</td>
    </tr>
  @endforeach
  </tbody>
</table>

<h5 class="mt-4">By Service</h5>
<table class="table table-sm table-bordered">
  <thead><tr><th>Service</th><th>Gross Revenue</th><th>After Mandatory</th><th>Share Pool</th></tr></thead>
  <tbody>
  @foreach($byService as $r)
    <tr>
      <td>{{ $r->service }}</td>
      <td>@naira($r->A ?? 0)</td>
      <td>@naira($r->RA ?? 0)</td>
      <td>@naira($r->RS ?? 0)</td>
    </tr>
  @endforeach
  </tbody>
</table>

<h5 class="mt-4">Revenue Lines (YTD)</h5>
<table class="table table-sm table-striped">
  <thead>
    <tr>
      <th>Date</th><th>Service</th><th>MNO</th><th>Aggregator</th>
      <th>Gross Revenue</th><th>Aggregator %</th><th>Aggregator Net</th><th>Share Pool</th><th></th>
    </tr>
  </thead>
  <tbody>
  @foreach($list as $it)
    <tr>
      <td>{{ $it->payment_date }}</td>
      <td>{{ optional($it->service)->name }}</td>
      <td>{{ optional($it->mno)->name }}</td>
      <td>{{ optional($it->aggregator)->name }}</td>
      <td>@naira($it->gross_revenue_a ?? 0)</td>
      <td>{{ number_format($it->aggregator_percentage,2) }}%</td>
      <td>@naira($it->aggregator_net_x ?? 0)</td>
      <td>
        @if(optional($it->partnerShareSummary)->rs_share_pool)
          @naira($it->partnerShareSummary->rs_share_pool)
        @else
          -
        @endif
      </td>
      <td><a href="{{ route('revenue.show',$it->id) }}">Open</a></td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $list->links() }}
@endsection
