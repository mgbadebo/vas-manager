@php($recipientOldId = (int) old('recipient_id'))
<div class="card" id="recipients">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Operational Expense Recipients</span>
        <small class="text-muted">Vendors or stakeholders receiving operational payouts.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.recipients.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Name / Company</label>
                <input type="text" name="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror"
                    value="{{ old('recipient_name') }}" placeholder="e.g. SMS Vendor Ltd" required>
                @error('recipient_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="recipient_category_id" class="form-select @error('recipient_category_id') is-invalid @enderror" required>
                    <option value="">-- select category --</option>
                    @foreach($operationalCategories as $category)
                        <option value="{{ $category->id }}" @selected(old('recipient_category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('recipient_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add Recipient</button>
            </div>
        </form>

        <hr>

        @forelse($expenseRecipients as $recipient)
            @php($isEditing = $recipientOldId === $recipient->id)
            <div class="border rounded p-3 mb-3">
                <form method="POST" action="{{ route('admin.settings.recipients.update', $recipient) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="recipient_id" value="{{ $recipient->id }}">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="recipient_name"
                            class="form-control @if($isEditing) @error('recipient_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('recipient_name') : $recipient->name }}" required>
                        @if($isEditing)
                            @error('recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="recipient_category_id"
                            class="form-select @if($isEditing) @error('recipient_category_id') is-invalid @enderror @endif" required>
                            @foreach($operationalCategories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(($isEditing ? old('recipient_category_id') : $recipient->operational_category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($isEditing)
                            @error('recipient_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                        <button class="btn btn-outline-danger btn-sm ms-2" type="submit"
                            form="delete-recipient-{{ $recipient->id }}"
                            onclick="return confirm('Delete this recipient?')">Delete</button>
                    </div>
                </form>
                <small class="text-muted">Category: {{ $recipient->operationalCategory->name ?? 'Unassigned' }}</small>
                <form id="delete-recipient-{{ $recipient->id }}" method="POST"
                    action="{{ route('admin.settings.recipients.destroy', $recipient) }}">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <p class="text-muted mb-0">No recipients created yet.</p>
        @endforelse
    </div>
</div>

