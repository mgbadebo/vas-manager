@php($bankOldId = (int) old('bank_id'))
<div class="card" id="banks">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Banks</span>
        <small class="text-muted">Where payments are received.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.banks.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Name</label>
                <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                    value="{{ old('bank_name') }}" placeholder="e.g. Zenith Bank" required>
                @error('bank_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Account Number (optional)</label>
                <input type="text" name="bank_account_number"
                    class="form-control @error('bank_account_number') is-invalid @enderror"
                    value="{{ old('bank_account_number') }}" placeholder="e.g. 0123456789">
                @error('bank_account_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Currency</label>
                <input type="text" name="bank_currency"
                    class="form-control @error('bank_currency') is-invalid @enderror"
                    value="{{ old('bank_currency', 'NGN') }}" placeholder="e.g. NGN" maxlength="10">
                @error('bank_currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary">Add Bank</button>
            </div>
        </form>

        <hr>

        @forelse($banks as $bank)
            @php($isEditing = $bankOldId === $bank->id)
            <div class="border rounded p-3 mb-3">
                <form method="POST" action="{{ route('admin.settings.banks.update', $bank) }}" class="row g-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="bank_id" value="{{ $bank->id }}">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="bank_name"
                            class="form-control @if($isEditing) @error('bank_name') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('bank_name') : $bank->name }}" required>
                        @if($isEditing)
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number"
                            class="form-control @if($isEditing) @error('bank_account_number') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('bank_account_number') : $bank->account_number }}">
                        @if($isEditing)
                            @error('bank_account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Currency</label>
                        <input type="text" name="bank_currency"
                            class="form-control @if($isEditing) @error('bank_currency') is-invalid @enderror @endif"
                            value="{{ $isEditing ? old('bank_currency') : $bank->currency }}" maxlength="10" required>
                        @if($isEditing)
                            @error('bank_currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-1 text-end">
                        <button class="btn btn-outline-primary btn-sm">Save</button>
                    </div>
                </form>
                <form id="delete-bank-{{ $bank->id }}" method="POST" action="{{ route('admin.settings.banks.destroy', $bank) }}">
                    @csrf
                    @method('DELETE')
                </form>
                <div class="text-end mt-2">
                    <button class="btn btn-outline-danger btn-sm" type="submit"
                        form="delete-bank-{{ $bank->id }}"
                        onclick="return confirm('Delete this bank?')">Delete</button>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No banks added yet.</p>
        @endforelse
    </div>
</div>

