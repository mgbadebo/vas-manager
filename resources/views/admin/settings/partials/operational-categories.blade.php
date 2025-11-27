@php($categoryOldId = (int) old('operational_category_id'))
<div class="card" id="operational-categories">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Operational Expense Categories</span>
        <small class="text-muted">Buckets for operational deductions.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.operational-categories.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-12">
                <label class="form-label">Name</label>
                <input type="text" name="operational_category_name"
                    class="form-control @error('operational_category_name') is-invalid @enderror"
                    value="{{ old('operational_category_name') }}" placeholder="e.g. Salaries" required>
                @error('operational_category_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add Category</button>
            </div>
        </form>

        <hr>

        @forelse($operationalCategories as $category)
            @php($isEditing = $categoryOldId === $category->id)
            <div class="border rounded p-3 mb-3">
                <form method="POST" action="{{ route('admin.settings.operational-categories.update', $category) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="operational_category_id" value="{{ $category->id }}">
                    <div class="col-md-9">
                        <label class="form-label">Name</label>
                        <input type="text" name="operational_category_name"
                            class="form-control @if($isEditing) @error('operational_category_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('operational_category_name') : $category->name }}" required>
                        @if($isEditing)
                            @error('operational_category_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                        <button class="btn btn-outline-danger btn-sm ms-2" type="submit"
                            form="delete-operational-category-{{ $category->id }}"
                            onclick="return confirm('Delete this category?')">Delete</button>
                    </div>
                </form>
                <form id="delete-operational-category-{{ $category->id }}" method="POST"
                    action="{{ route('admin.settings.operational-categories.destroy', $category) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <p class="text-muted mb-0">No operational categories created yet.</p>
        @endforelse
    </div>
</div>

