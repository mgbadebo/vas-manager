@extends('layouts.bootstrap')

@section('content')
<h1 class="h4 mb-3">Services</h1>
<ul class="list-group">
  @foreach($rows as $r)
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <span>{{ $r->name }}</span>
      @if($r->serviceType)
        <span class="badge bg-primary">{{ $r->serviceType->name }}</span>
      @else
        <span class="badge bg-secondary">No Type</span>
      @endif
    </li>
  @endforeach
</ul>
@endsection
