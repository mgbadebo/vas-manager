@php($aggregatorOldId = (int) old('aggregator_id'))
<div class="card" id="aggregators">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Aggregators</span>
        <small class="text-muted">Partners distributing services.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.aggregators.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-7">
                <label class="form-label">Name</label>
                <input type="text" name="aggregator_name" class="form-control @error('aggregator_name') is-invalid @enderror"
                    value="{{ old('aggregator_name') }}" placeholder="e.g. VAS2Nets" required>
                @error('aggregator_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-5">
                <label class="form-label">Short Code (optional)</label>
                <input type="text" name="aggregator_short_code"
                    class="form-control @error('aggregator_short_code') is-invalid @enderror"
                    value="{{ old('aggregator_short_code') }}" placeholder="e.g. V2N">
                @error('aggregator_short_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add Aggregator</button>
            </div>
        </form>

        <hr>

        @forelse($aggregators as $aggregator)
            @php($isEditing = $aggregatorOldId === $aggregator->id)
            <div class="border rounded p-3 mb-3">
                <form method="POST" action="{{ route('admin.settings.aggregators.update', $aggregator) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="aggregator_id" value="{{ $aggregator->id }}">

                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="aggregator_name"
                            class="form-control @if($isEditing) @error('aggregator_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('aggregator_name') : $aggregator->name }}" required>
                        @if($isEditing)
                            @error('aggregator_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Short Code</label>
                        <input type="text" name="aggregator_short_code"
                            class="form-control @if($isEditing) @error('aggregator_short_code') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('aggregator_short_code') : $aggregator->short_code }}">
                        @if($isEditing)
                            @error('aggregator_short_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                        <button class="btn btn-outline-danger btn-sm ms-2" type="submit"
                            form="delete-aggregator-{{ $aggregator->id }}"
                            onclick="return confirm('Delete this aggregator?')">Delete</button>
                    </div>
                </form>
                <form id="delete-aggregator-{{ $aggregator->id }}" method="POST"
                    action="{{ route('admin.settings.aggregators.destroy', $aggregator) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <p class="text-muted mb-0">No aggregators created yet.</p>
        @endforelse
    </div>
</div>

