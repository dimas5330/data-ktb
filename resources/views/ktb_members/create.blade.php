<x-layouts.app title="Buat Anggota KTB">
    <div class="space-y-6">
        <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Buat Anggota KTB</h1>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <form action="{{ route('ktb-members.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Nama <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                    @error('name')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                    @error('email')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                    @error('phone')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Generation</label>
                    <input type="number" name="generation" min="1" value="{{ old('generation', 1) }}"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                    @error('generation')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-900 dark:text-white">Status</label>
                    <select name="status"
                        class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-4 py-2 text-zinc-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:focus:border-blue-400">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="alumni" {{ old('status') == 'alumni' ? 'selected' : '' }}>Alumni</option>
                    </select>
                    @error('status')<div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>@enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="{{ route('ktb-members.index') }}" class="rounded-lg bg-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-white dark:hover:bg-zinc-600">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
