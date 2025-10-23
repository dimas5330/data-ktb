<x-layouts.app title="Tambah Mentee">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Tambah Mentee untuk {{ $mentor->name }}</h1>
            <a href="{{ route('ktb-members.show', $mentor) }}" class="rounded-lg bg-zinc-600 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">
                Kembali
            </a>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <form action="{{ route('ktb-members.store-mentee', $mentor) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <!-- Pilih Mentee -->
                    <div>
                        <label for="mentee_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Pilih Mentee <span class="text-red-600">*</span></label>
                        <select name="mentee_id" id="mentee_id" required
                            class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                            <option value="">-- Pilih Mentee --</option>
                            @foreach($availableMembers as $member)
                                <option value="{{ $member->id }}" {{ old('mentee_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} (Gen {{ $member->generation }})
                                </option>
                            @endforeach
                        </select>
                        @error('mentee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelompok -->
                    <div>
                        <label for="group_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Kelompok</label>
                        <select name="group_id" id="group_id"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                            <option value="">-- Pilih Kelompok --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('group_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Relasi -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status Relasi <span class="text-red-600">*</span></label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                            <option value="rutin" {{ old('status') == 'rutin' ? 'selected' : '' }}>Rutin</option>
                            <option value="tidak rutin" {{ old('status') == 'tidak rutin' ? 'selected' : '' }}>Tidak Rutin</option>
                            <option value="dipotong" {{ old('status') == 'dipotong' ? 'selected' : '' }}>Dipotong</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="started_at" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Tanggal Mulai</label>
                        <input type="date" name="started_at" id="started_at" value="{{ old('started_at') }}"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                        @error('started_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label for="ended_at" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Tanggal Selesai</label>
                        <input type="date" name="ended_at" id="ended_at" value="{{ old('ended_at') }}"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                        @error('ended_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Catatan</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-zinc-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="{{ route('ktb-members.show', $mentor) }}" class="rounded-lg bg-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
