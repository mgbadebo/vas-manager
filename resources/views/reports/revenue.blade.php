@extends('layouts.bootstrap')

@section('content')
<h1 class="h4 mb-3">Revenue Report ({{ $start->format('Y-m-d') }} → {{ $end->format('Y-m-d') }})</h1>

<a href="{{ route('revenue.create') }}" class="btn btn-primary mb-4">
    <i class="bi bi-plus-circle"></i> Add Revenue Entry
</a>

<h5 class="mt-4">By Aggregator</h5>
<table class="table table-sm table-bordered">
  <thead><tr><th>Aggregator</th><th>A</th><th>RA</th><th>RS</th></tr></thead>
  <tbody>
  @foreach($byAggregator as $r)
    <tr>
      <td>{{ $r->aggregator }}</td>
      <td>{{ number_format($r->A,2) }}</td>
      <td>{{ number_format($r->RA,2) }}</td>
      <td>{{ number_format($r->RS,2) }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

<h5 class="mt-4">By MNO</h5>
<table class="table table-sm table-bordered">
  <thead><tr><th>MNO</th><th>A</th><th>RA</th><th>RS</th></tr></thead>
  <tbody>
  @foreach($byMNO as $r)
    <tr>
      <td>{{ $r->mno }}</td>
      <td>{{ number_format($r->A,2) }}</td>
      <td>{{ number_format($r->RA,2) }}</td>
      <td>{{ number_format($r->RS,2) }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

<h5 class="mt-4">By Service</h5>
<table class="table table-sm table-bordered">
  <thead><tr><th>Service</th><th>A</th><th>RA</th><th>RS</th></tr></thead>
  <tbody>
  @foreach($byService as $r)
    <tr>
      <td>{{ $r->service }}</td>
      <td>{{ number_format($r->A,2) }}</td>
      <td>{{ number_format($r->RA,2) }}</td>
      <td>{{ number_format($r->RS,2) }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

<h5 class="mt-4">Revenue Lines (YTD)</h5>
<table class="table table-sm table-striped">
  <thead>
    <tr>
      <th>Date</th><th>Service</th><th>MNO</th><th>Aggregator</th>
      <th>A</th><th>%A</th><th>X</th><th>RS</th><th></th>
    </tr>
  </thead>
  <tbody>
  @foreach($list as $it)
    <tr>
      <td>{{ $it->payment_date }}</td>
      <td>{{ optional($it->service)->name }}</td>
      <td>{{ optional($it->mno)->name }}</td>
      <td>{{ optional($it->aggregator)->name }}</td>
      <td>{{ number_format($it->gross_revenue_a,2) }}</td>
      <td>{{ number_format($it->aggregator_percentage,2) }}%</td>
      <td>{{ number_format($it->aggregator_net_x,2) }}</td>
      <td>{{ optional($it->partnerShareSummary)->rs_share_pool ? number_format($it->partnerShareSummary->rs_share_pool,2) : '-' }}</td>
      <td><a href="{{ route('revenue.show',$it->id) }}">Open</a></td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $list->links() }}
@endsection
