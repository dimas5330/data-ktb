<x-layouts.app title="Edit Anggota KTB">
    <div class="space-y-6">
        <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Edit Anggota: {{ $member->name }}</h1>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <form action="{{ route('ktb-members.update', $member) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Nama <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                    @error('name')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Email</label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                    @error('email')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                    @error('phone')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Kelompok KTB</label>
                    <select name="current_group_id"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                        <option value="">-- Pilih Kelompok --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('current_group_id', $member->current_group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        💡 Generation saat ini: <strong>Gen {{ $member->generation ?? 'Auto' }}</strong> (otomatis di-calculate dari mentor)
                    </p>
                    @error('current_group_id')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Status</label>
                    <select name="status"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                        <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="alumni" {{ old('status', $member->status) == 'alumni' ? 'selected' : '' }}>Alumni</option>
                    </select>
                    @error('status')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Update
                    </button>
                    <a href="{{ route('ktb-members.index') }}" class="rounded-lg bg-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-white dark:hover:bg-zinc-600">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
