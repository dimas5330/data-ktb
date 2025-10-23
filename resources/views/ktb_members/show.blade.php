<x-layouts.app title="Detail Anggota KTB">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Detail Anggota: {{ $member->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('ktb-members.edit', $member) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('ktb-members.index') }}" class="rounded-lg bg-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-white dark:hover:bg-zinc-600">
                    Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <!-- Grid Layout: Main Content + Sidebar -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="mb-4 text-xl font-semibold text-zinc-900 dark:text-white">Informasi Anggota</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-700">
                            <div class="font-semibold text-zinc-700 dark:text-zinc-300">Nama</div>
                            <div class="col-span-2 text-zinc-900 dark:text-white">{{ $member->name }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-700">
                            <div class="font-semibold text-zinc-700 dark:text-zinc-300">Email</div>
                            <div class="col-span-2 text-zinc-900 dark:text-white">{{ $member->email ?? '-' }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-700">
                            <div class="font-semibold text-zinc-700 dark:text-zinc-300">Phone</div>
                            <div class="col-span-2 text-zinc-900 dark:text-white">{{ $member->phone ?? '-' }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-700">
                            <div class="font-semibold text-zinc-700 dark:text-zinc-300">Generation</div>
                            <div class="col-span-2">
                                <span class="inline-flex rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300">
                                    Gen {{ $member->generation }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-700">
                            <div class="font-semibold text-zinc-700 dark:text-zinc-300">Status</div>
                            <div class="col-span-2">
                                @if($member->status === 'active')
                                    <span class="inline-flex rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                @elseif($member->status === 'alumni')
                                    <span class="inline-flex rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Alumni</span>
                                @else
                                    <span class="inline-flex rounded-md bg-red-100 px-2 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-700">
                            <div class="font-semibold text-zinc-700 dark:text-zinc-300">Group Saat Ini</div>
                            <div class="col-span-2 text-zinc-900 dark:text-white">{{ $member->currentGroup?->name ?? '-' }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="font-semibold text-zinc-700 dark:text-zinc-300">Jumlah Mentees</div>
                            <div class="col-span-2">
                                <span class="inline-flex rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $member->mentees()->count() }} mentees
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Daftar Mentees -->
            <div class="lg:col-span-1">
                <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 sticky top-6">
                    <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                Daftar Mentees
                            </h2>
                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                {{ $member->mentees()->count() }}
                            </span>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('ktb-members.add-mentee', $member) }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Mentee
                            </a>
                        </div>
                    </div>

                    <div class="max-h-[600px] overflow-y-auto">
                        @if($member->mentees()->count() > 0)
                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($member->mentees as $mentee)
                                    @php
                                        $relationship = $member->mentoringRelationships()
                                            ->where('mentee_id', $mentee->id)
                                            ->first();
                                    @endphp
                                    <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('ktb-members.show', $mentee) }}" class="font-medium text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">
                                                        {{ $mentee->name }}
                                                    </a>
                                                </div>
                                                <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">
                                                    Gen {{ $mentee->generation }} • {{ $relationship?->group?->name ?? 'No Group' }}
                                                </div>
                                                <div class="mt-2 flex items-center gap-2">
                                                    @if($relationship?->status === 'rutin')
                                                        <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-900/20 dark:text-green-400">Rutin</span>
                                                    @elseif($relationship?->status === 'tidak rutin')
                                                        <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">Tidak Rutin</span>
                                                    @else
                                                        <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-900/20 dark:text-red-400">Dipotong</span>
                                                    @endif
                                                </div>
                                                @if($relationship?->started_at)
                                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-500">
                                                        {{ $relationship->started_at->format('d M Y') }}
                                                        @if($relationship->ended_at)
                                                            - {{ $relationship->ended_at->format('d M Y') }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex flex-col gap-1 ml-2">
                                                <a href="{{ route('ktb-members.edit-mentee', [$member, $relationship->id]) }}"
                                                   class="rounded p-1 text-yellow-600 hover:bg-yellow-50 dark:text-yellow-400 dark:hover:bg-yellow-900/20"
                                                   title="Edit">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('ktb-members.destroy-mentee', [$member, $relationship->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus relasi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="rounded p-1 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                            title="Hapus">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Belum ada mentee</p>
                                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Klik tombol di atas untuk menambahkan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
