@php($typeOldId = (int) old('mandatory_type_id'))
<div class="card" id="mandatory-types">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Mandatory Expense Types</span>
        <small class="text-muted">Controls how mandatory deductions are calculated.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.mandatory-types.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="mandatory_type_name" class="form-control @error('mandatory_type_name') is-invalid @enderror"
                    value="{{ old('mandatory_type_name') }}" placeholder="e.g. VAT" required>
                @error('mandatory_type_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Rule</label>
                <select name="mandatory_type_rule" class="form-select @error('mandatory_type_rule') is-invalid @enderror" required>
                    <option value="">-- choose --</option>
                    @foreach($ruleTypes as $rule)
                        <option value="{{ $rule }}" @selected(old('mandatory_type_rule') === $rule)>
                            {{ str_replace('_', ' ', $rule) }}
                        </option>
                    @endforeach
                </select>
                @error('mandatory_type_rule')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add Type</button>
            </div>
        </form>

        <hr>

        @forelse($mandatoryTypes as $type)
            @php($isEditing = $typeOldId === $type->id)
            <div class="border rounded p-3 mb-3">
                <form method="POST" action="{{ route('admin.settings.mandatory-types.update', $type) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="mandatory_type_id" value="{{ $type->id }}">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="mandatory_type_name"
                            class="form-control @if($isEditing) @error('mandatory_type_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('mandatory_type_name') : $type->name }}" required>
                        @if($isEditing)
                            @error('mandatory_type_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Rule</label>
                        <select name="mandatory_type_rule"
                            class="form-select @if($isEditing) @error('mandatory_type_rule') is-invalid @enderror @endif" required>
                            @foreach($ruleTypes as $rule)
                                <option value="{{ $rule }}" @selected(($isEditing ? old('mandatory_type_rule') : $type->rule_type) === $rule)>
                                    {{ str_replace('_', ' ', $rule) }}
                                </option>
                            @endforeach
                        </select>
                        @if($isEditing)
                            @error('mandatory_type_rule')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                        <button class="btn btn-outline-danger btn-sm ms-2" type="submit"
                            form="delete-mandatory-type-{{ $type->id }}"
                            onclick="return confirm('Delete this type?')">Delete</button>
                    </div>
                </form>
                <form id="delete-mandatory-type-{{ $type->id }}" method="POST"
                    action="{{ route('admin.settings.mandatory-types.destroy', $type) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <p class="text-muted mb-0">No mandatory expense types configured.</p>
        @endforelse
    </div>
</div>

