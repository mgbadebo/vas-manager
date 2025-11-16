@extends('layouts.bootstrap')

@section('content')
<h1 class="h4 mb-3">Aggregators</h1>
<ul class="list-group">
  @foreach($rows as $r)
    <li class="list-group-item">{{ $r->name }}</li>
  @endforeach
</ul>
@endsection
