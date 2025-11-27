@php($serviceTypeOldId = (int) old('service_type_id'))
<div class="card" id="service-types">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Service Types</span>
        <small class="text-muted">Categories for classifying services (e.g. Lottery, Games, Casino).</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.service-types.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-12">
                <label class="form-label">Name</label>
                <input type="text" name="service_type_name" class="form-control @error('service_type_name') is-invalid @enderror"
                    value="{{ old('service_type_name') }}" placeholder="e.g. Lottery" required>
                @error('service_type_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add Service Type</button>
            </div>
        </form>

        <hr>

        @forelse($serviceTypes as $serviceType)
            @php($isEditing = $serviceTypeOldId === $serviceType->id)
            <div class="border rounded p-3 mb-3">
                <form method="POST" action="{{ route('admin.settings.service-types.update', $serviceType) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="service_type_id" value="{{ $serviceType->id }}">
                    <div class="col-md-9">
                        <label class="form-label">Name</label>
                        <input type="text" name="service_type_name"
                            class="form-control @if($isEditing) @error('service_type_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('service_type_name') : $serviceType->name }}" required>
                        @if($isEditing)
                            @error('service_type_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                        <button class="btn btn-outline-danger btn-sm ms-2" type="submit"
                            form="delete-service-type-{{ $serviceType->id }}"
                            onclick="return confirm('Delete this service type?')">Delete</button>
                    </div>
                </form>
                <form id="delete-service-type-{{ $serviceType->id }}" method="POST"
                    action="{{ route('admin.settings.service-types.destroy', $serviceType) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <p class="text-muted mb-0">No service types created yet.</p>
        @endforelse
    </div>
</div>

