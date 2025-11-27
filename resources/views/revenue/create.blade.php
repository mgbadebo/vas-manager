@extends('layouts.bootstrap')

@section('content')
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
      <h1 class="h3 mb-1 fw-bold">Add Revenue Entry</h1>
      <p class="text-muted mb-0 small">Create a new revenue record</p>
    </div>
    <a href="{{ route('revenue.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">
      <i class="bi bi-arrow-left"></i> Back
    </a>
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

  <form method="post" action="{{ route('revenue.store') }}" class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
      <h5 class="card-title mb-0 fw-semibold">
        <i class="bi bi-file-earmark-plus text-primary"></i> Revenue Information
      </h5>
    </div>
    <div class="card-body p-4">
    @csrf

    <div class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Service</label>
        <select name="service_id" class="form-select" required>
          <option value="">-- select --</option>
          @foreach($services as $id => $name)
            <option value="{{ $id }}" @selected(old('service_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">MNO</label>
        <select name="mno_id" class="form-select" required>
          <option value="">-- select --</option>
          @foreach($mnos as $id => $name)
            <option value="{{ $id }}" @selected(old('mno_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Aggregator</label>
        <select name="aggregator_id" class="form-select" required>
          <option value="">-- select --</option>
          @foreach($aggregators as $id => $name)
            <option value="{{ $id }}" @selected(old('aggregator_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Payment Date (actual receipt)</label>
        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date') }}">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Payment Period (Month)</label>
        <select name="payment_period_month" class="form-select" required>
          <option value="">-- select month --</option>
          @foreach(range(1,12) as $month)
            <option value="{{ $month }}" @selected(old('payment_period_month') == $month)>
              {{ \Carbon\Carbon::create()->month($month)->format('F') }}
            </option>
          @endforeach
        </select>
        @error('payment_period_month')
          <div class="text-danger small">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Payment Period (Year)</label>
        <select name="payment_period_year" class="form-select" required>
          <option value="">-- select year --</option>
          @foreach(range(date('Y') + 1, date('Y') - 5) as $year)
            <option value="{{ $year }}" @selected(old('payment_period_year') == $year)>{{ $year }}</option>
          @endforeach
        </select>
        @error('payment_period_year')
          <div class="text-danger small">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Bank</label>
        <select name="bank_id" class="form-select" required>
          <option value="">-- select bank --</option>
          @foreach($banks as $id => $name)
            <option value="{{ $id }}" @selected(old('bank_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
        @error('bank_id')
          <div class="text-danger small">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Gross Revenue</label>
        <input type="number" step="0.01" name="gross_revenue_a" class="form-control" value="{{ old('gross_revenue_a') }}" required>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Aggregator Percentage</label>
        <input type="number" step="0.0001" name="aggregator_percentage" class="form-control" value="{{ old('aggregator_percentage') }}" required>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Aggregator Net (auto-calculated)</label>
        <input type="text" id="aggregator_net_x_preview" class="form-control" value="0.00" readonly>
        <div class="form-text">Calculated as Gross Revenue × (1 − Aggregator %/100). Saved automatically.</div>
      </div>
    </div>

    </div>
    <div class="card-footer bg-white border-top">
      <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
        <a href="{{ route('revenue.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">Cancel</a>
        <button type="submit" class="btn btn-primary w-100 w-md-auto">
          <i class="bi bi-check2-circle"></i> Save Revenue Entry
        </button>
      </div>
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

