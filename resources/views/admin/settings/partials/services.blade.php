@php($serviceOldId = (int) old('service_id'))
@php($shareOldServiceId = (int) old('share_service_id'))
@php($shareOldEntryId = (int) old('share_entry_id'))
<div class="card" id="services">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Services</span>
        <small class="text-muted">Used to classify revenue lines.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.services.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-7">
                <label class="form-label">Name</label>
                <input type="text" name="service_name" class="form-control @error('service_name') is-invalid @enderror"
                    value="{{ old('service_name') }}" placeholder="e.g. Lottery" required>
                @error('service_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-5">
                <label class="form-label">Type</label>
                <select name="service_type_id" class="form-select @error('service_type_id') is-invalid @enderror">
                    <option value="">-- select --</option>
                    @foreach($serviceTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('service_type_id') == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('service_type_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add Service</button>
            </div>
        </form>

        <hr>

        @forelse($services as $service)
            @php($isEditing = $serviceOldId === $service->id)
            <div class="border rounded p-3 mb-4">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                        <h5 class="mb-0 fw-bold">{{ $service->name }}</h5>
                        @if($service->serviceType)
                            <span class="badge bg-primary fs-6">{{ $service->serviceType->name }}</span>
                        @else
                            <span class="badge bg-secondary fs-6">No Type</span>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.settings.services.update', $service) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    <div class="col-md-5">
                        <label class="form-label">Name</label>
                        <input type="text" name="service_name"
                            class="form-control @if($isEditing) @error('service_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('service_name') : $service->name }}" required>
                        @if($isEditing)
                            @error('service_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select name="service_type_id"
                            class="form-select @if($isEditing) @error('service_type_id') is-invalid @enderror @endif">
                            <option value="">-- select --</option>
                            @foreach($serviceTypes as $type)
                                <option value="{{ $type->id }}"
                                    @selected(($isEditing ? old('service_type_id') : $service->service_type_id) == $type->id)>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($isEditing)
                            @error('service_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                        <button class="btn btn-outline-danger btn-sm ms-2" type="submit"
                            form="delete-service-{{ $service->id }}"
                            onclick="return confirm('Delete this service?')">Delete</button>
                    </div>
                </form>
                <form id="delete-service-{{ $service->id }}" method="POST"
                    action="{{ route('admin.settings.services.destroy', $service) }}">
                    @csrf
                    @method('DELETE')
                </form>

                <div class="bg-light rounded p-3 mt-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="fw-semibold">Founding partner share schedules</div>
                            <small class="text-muted">New schedules apply to revenues dated on or after their effective date.</small>
                        </div>
                        @if($service->latestPartnerShare)
                            <span class="badge bg-secondary-subtle text-dark">
                                Current: DR {{ number_format($service->latestPartnerShare->dr_share, 2) }}% ·
                                AJ {{ number_format($service->latestPartnerShare->aj_share, 2) }}% ·
                                TJ {{ number_format($service->latestPartnerShare->tj_share, 2) }}%
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.settings.services.shares.store', $service) }}" class="row g-2 align-items-end border rounded p-3 mb-3 bg-white">
                        @csrf
                        <input type="hidden" name="share_service_id" value="{{ $service->id }}">
                        <div class="col-md-3">
                            <label class="form-label">Effective From</label>
                            <input type="date" name="effective_from"
                                class="form-control @if($shareOldServiceId === $service->id && !$shareOldEntryId) @error('effective_from') is-invalid @enderror @endif"
                                value="{{ $shareOldServiceId === $service->id && !$shareOldEntryId ? old('effective_from') : now()->toDateString() }}" required>
                            @if($shareOldServiceId === $service->id && !$shareOldEntryId)
                                @error('effective_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">DR (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="dr_share"
                                class="form-control @if($shareOldServiceId === $service->id && !$shareOldEntryId) @error('dr_share') is-invalid @enderror @endif"
                                value="{{ $shareOldServiceId === $service->id && !$shareOldEntryId ? old('dr_share') : (optional($service->latestPartnerShare)->dr_share ?? 50) }}" required>
                            @if($shareOldServiceId === $service->id && !$shareOldEntryId)
                                @error('dr_share')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">AJ (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="aj_share"
                                class="form-control @if($shareOldServiceId === $service->id && !$shareOldEntryId) @error('aj_share') is-invalid @enderror @endif"
                                value="{{ $shareOldServiceId === $service->id && !$shareOldEntryId ? old('aj_share') : (optional($service->latestPartnerShare)->aj_share ?? 30) }}" required>
                            @if($shareOldServiceId === $service->id && !$shareOldEntryId)
                                @error('aj_share')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">TJ (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="tj_share"
                                class="form-control @if($shareOldServiceId === $service->id && !$shareOldEntryId) @error('tj_share') is-invalid @enderror @endif"
                                value="{{ $shareOldServiceId === $service->id && !$shareOldEntryId ? old('tj_share') : (optional($service->latestPartnerShare)->tj_share ?? 20) }}" required>
                            @if($shareOldServiceId === $service->id && !$shareOldEntryId)
                                @error('tj_share')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-sm btn-primary">Add Schedule</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Effective From</th>
                                    <th>DR %</th>
                                    <th>AJ %</th>
                                    <th>TJ %</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($service->partnerShares as $entry)
                                    @php($editingRow = $shareOldServiceId === $service->id && $shareOldEntryId === $entry->id)
                                    <tr>
                                        <td colspan="5">
                                            <form method="POST" action="{{ route('admin.settings.services.shares.update', $entry) }}" class="row g-2 align-items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="share_service_id" value="{{ $service->id }}">
                                                <input type="hidden" name="share_entry_id" value="{{ $entry->id }}">
                                                <div class="col-md-3">
                                                    <input type="date" name="effective_from"
                                                        class="form-control @if($editingRow) @error('effective_from') is-invalid @enderror @endif"
                                                        value="{{ $editingRow ? old('effective_from') : $entry->effective_from->toDateString() }}" required>
                                                    @if($editingRow)
                                                        @error('effective_from')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" step="0.01" min="0" max="100" name="dr_share"
                                                        class="form-control @if($editingRow) @error('dr_share') is-invalid @enderror @endif"
                                                        value="{{ $editingRow ? old('dr_share') : $entry->dr_share }}" required>
                                                    @if($editingRow)
                                                        @error('dr_share')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" step="0.01" min="0" max="100" name="aj_share"
                                                        class="form-control @if($editingRow) @error('aj_share') is-invalid @enderror @endif"
                                                        value="{{ $editingRow ? old('aj_share') : $entry->aj_share }}" required>
                                                    @if($editingRow)
                                                        @error('aj_share')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" step="0.01" min="0" max="100" name="tj_share"
                                                        class="form-control @if($editingRow) @error('tj_share') is-invalid @enderror @endif"
                                                        value="{{ $editingRow ? old('tj_share') : $entry->tj_share }}" required>
                                                    @if($editingRow)
                                                        @error('tj_share')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <button class="btn btn-outline-primary btn-sm">Save</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted text-center">No schedules yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No services created yet.</p>
        @endforelse
    </div>
</div>

