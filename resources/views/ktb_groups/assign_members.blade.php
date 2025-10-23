<x-layouts.app title="Assign Anggota ke Kelompok">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Assign Anggota ke Kelompok</h1>
            <a href="{{ route('ktb-groups.show', $ktbGroup) }}" class="rounded-lg bg-zinc-600 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">
                Kembali
            </a>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $ktbGroup->name }}</h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Saat ini: {{ $ktbGroup->members->count() }} anggota
                </p>
            </div>

            <form action="{{ route('ktb-groups.update-members', $ktbGroup) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Pilih Anggota
                    </label>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-3">
                        Centang anggota yang ingin ditambahkan ke kelompok ini
                    </p>

                    <div class="space-y-2 max-h-96 overflow-y-auto rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        @forelse($availableMembers as $member)
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="member_ids[]"
                                    value="{{ $member->id }}"
                                    id="member_{{ $member->id }}"
                                    {{ $member->current_group_id == $ktbGroup->id ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800"
                                >
                                <label for="member_{{ $member->id }}" class="ml-3 flex-1 cursor-pointer">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $member->name }}</span>
                                    <span class="text-xs text-zinc-600 dark:text-zinc-400">
                                        (Gen {{ $member->generation }})
                                        @if($member->current_group_id == $ktbGroup->id)
                                            <span class="ml-1 text-green-600 dark:text-green-400">• Sudah di kelompok ini</span>
                                        @elseif($member->current_group_id)
                                            <span class="ml-1 text-yellow-600 dark:text-yellow-400">• Sudah di kelompok lain</span>
                                        @else
                                            <span class="ml-1 text-zinc-500 dark:text-zinc-400">• Belum ada kelompok</span>
                                        @endif
                                    </span>
                                </label>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Tidak ada anggota tersedia</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('ktb-groups.show', $ktbGroup) }}" class="rounded-lg bg-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/50 dark:bg-blue-900/20">
            <h3 class="mb-2 text-sm font-semibold text-blue-900 dark:text-blue-300">Informasi:</h3>
            <ul class="list-inside list-disc space-y-1 text-xs text-blue-800 dark:text-blue-400">
                <li>Anggota yang sudah dicentang akan ditambahkan ke kelompok ini</li>
                <li>Anggota yang tidak dicentang akan dihapus dari kelompok ini</li>
                <li>Anggota hanya bisa berada di satu kelompok dalam satu waktu</li>
                <li>Menambahkan anggota dari kelompok lain akan memindahkannya ke kelompok ini</li>
            </ul>
        </div>
    </div>
</x-layouts.app>
