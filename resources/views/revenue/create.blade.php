@extends('layouts.bootstrap')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Add Revenue Entry</h1>
    <a href="{{ route('revenue.index') }}" class="btn btn-outline-secondary">Back</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('revenue.store') }}" class="card shadow-sm p-3">
    @csrf

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Service</label>
        <select name="service_id" class="form-select" required>
          <option value="">-- select --</option>
          @foreach($services as $id => $name)
            <option value="{{ $id }}" @selected(old('service_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">MNO</label>
        <select name="mno_id" class="form-select" required>
          <option value="">-- select --</option>
          @foreach($mnos as $id => $name)
            <option value="{{ $id }}" @selected(old('mno_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Aggregator</label>
        <select name="aggregator_id" class="form-select" required>
          <option value="">-- select --</option>
          @foreach($aggregators as $id => $name)
            <option value="{{ $id }}" @selected(old('aggregator_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Payment Date</label>
        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date') }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Period Label (e.g. 2025-10)</label>
        <input type="text" name="period_label" class="form-control" value="{{ old('period_label') }}">
      </div>

      <div class="col-md-4">
        <label class="form-label">Gross Revenue A</label>
        <input type="number" step="0.01" name="gross_revenue_a" class="form-control" value="{{ old('gross_revenue_a') }}" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Aggregator % (A%)</label>
        <input type="number" step="0.0001" name="aggregator_percentage" class="form-control" value="{{ old('aggregator_percentage') }}" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Aggregator Net X (auto)</label>
        <input type="text" id="aggregator_net_x_preview" class="form-control" value="0.00" readonly>
        <div class="form-text">Calculated as A × (1 − %A/100). Saved automatically.</div>
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save</button>
    </div>
  </form>

  @push('scripts')
  <script>
    function recalcX(){
      const A  = parseFloat(document.querySelector('input[name="gross_revenue_a"]').value || 0);
      const pA = parseFloat(document.querySelector('input[name="aggregator_percentage"]').value || 0);
      const X  = A * (1 - (pA/100));
      document.getElementById('aggregator_net_x_preview').value = isFinite(X) ? X.toFixed(2) : '0.00';
    }
    document.addEventListener('input', (e) => {
      if (e.target.name === 'gross_revenue_a' || e.target.name === 'aggregator_percentage') recalcX();
    });
    document.addEventListener('DOMContentLoaded', recalcX);
  </script>
  @endpush
@endsection

