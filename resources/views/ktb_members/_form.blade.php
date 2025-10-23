@csrf

<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" class="form-control" required>
    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" value="{{ old('email', $member->email ?? '') }}" class="form-control">
    @error('email')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', $member->phone ?? '') }}" class="form-control">
    @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Generation</label>
    <input type="number" name="generation" value="{{ old('generation', $member->generation ?? 1) }}" class="form-control" min="1">
    @error('generation')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-control">
        <option value="active" {{ (old('status', $member->status ?? '')=='active') ? 'selected' : '' }}>active</option>
        <option value="inactive" {{ (old('status', $member->status ?? '')=='inactive') ? 'selected' : '' }}>inactive</option>
        <option value="alumni" {{ (old('status', $member->status ?? '')=='alumni') ? 'selected' : '' }}>alumni</option>
    </select>
    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('ktb-members.index') }}" class="btn btn-secondary">Batal</a>
</div>
