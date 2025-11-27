@php($mnoOldId = (int) old('mno_id'))
<div class="card" id="mnos">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">MNOs</span>
        <small class="text-muted">Mobile network operators tied to revenue entries.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.mnos.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-12">
                <label class="form-label">Name</label>
                <input type="text" name="mno_name" class="form-control @error('mno_name') is-invalid @enderror"
                    value="{{ old('mno_name') }}" placeholder="e.g. MTN" required>
                @error('mno_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add MNO</button>
            </div>
        </form>

        <hr>

        @forelse($mnos as $mno)
            @php($isEditing = $mnoOldId === $mno->id)
            <div class="border rounded p-3 mb-3">
                <form method="POST" action="{{ route('admin.settings.mnos.update', $mno) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="mno_id" value="{{ $mno->id }}">
                    <div class="col-md-9">
                        <label class="form-label">Name</label>
                        <input type="text" name="mno_name"
                            class="form-control @if($isEditing) @error('mno_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('mno_name') : $mno->name }}" required>
                        @if($isEditing)
                            @error('mno_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                        <button class="btn btn-outline-danger btn-sm ms-2" type="submit"
                            form="delete-mno-{{ $mno->id }}"
                            onclick="return confirm('Delete this MNO?')">Delete</button>
                    </div>
                </form>
                <form id="delete-mno-{{ $mno->id }}" method="POST"
                    action="{{ route('admin.settings.mnos.destroy', $mno) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <p class="text-muted mb-0">No MNOs created yet.</p>
        @endforelse
    </div>
</div>

