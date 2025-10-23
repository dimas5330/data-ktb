<x-layouts.app title="Detail Kelompok KTB">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Detail Kelompok KTB</h1>
            <div class="flex gap-2">
                <a href="{{ route('ktb-groups.assign-members', $ktbGroup) }}" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                    Assign Anggota
                </a>
                <a href="{{ route('ktb-groups.edit', $ktbGroup) }}" class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-700">
                    Edit
                </a>
                <a href="{{ route('ktb-groups.index') }}" class="rounded-lg bg-zinc-600 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">
                    Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <!-- Informasi Kelompok -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="mb-4 text-xl font-semibold text-zinc-900 dark:text-white">Informasi Kelompok</h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Nama Kelompok</p>
                    <p class="font-medium text-zinc-900 dark:text-white">{{ $ktbGroup->name }}</p>
                </div>

                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Pemimpin</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        @if($ktbGroup->leader)
                            <a href="{{ route('ktb-members.show', $ktbGroup->leader) }}" class="text-blue-600 hover:underline dark:text-blue-400">
                                {{ $ktbGroup->leader->name }}
                            </a>
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Lokasi</p>
                    <p class="font-medium text-zinc-900 dark:text-white">{{ $ktbGroup->location ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Jadwal Pertemuan</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        @if($ktbGroup->meeting_day && $ktbGroup->meeting_time)
                            {{ $ktbGroup->meeting_day }}, {{ $ktbGroup->meeting_time->format('H:i') }}
                        @elseif($ktbGroup->meeting_day)
                            {{ $ktbGroup->meeting_day }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Status</p>
                    <p class="font-medium">
                        @if($ktbGroup->status === 'active')
                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/20 dark:text-green-400">Active</span>
                        @elseif($ktbGroup->status === 'completed')
                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">Completed</span>
                        @else
                            <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/20 dark:text-red-400">Inactive</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Periode</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        @if($ktbGroup->started_at)
                            {{ $ktbGroup->started_at->format('d M Y') }}
                            @if($ktbGroup->ended_at)
                                - {{ $ktbGroup->ended_at->format('d M Y') }}
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>

                @if($ktbGroup->description)
                <div class="col-span-2">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Deskripsi</p>
                    <p class="font-medium text-zinc-900 dark:text-white">{{ $ktbGroup->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Daftar Anggota -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                    Anggota Kelompok ({{ $ktbGroup->members->count() }})
                </h2>
                <a href="{{ route('ktb-groups.assign-members', $ktbGroup) }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                    Kelola Anggota
                </a>
            </div>

            @if($ktbGroup->members->count() > 0)
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full">
                        <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Generasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Mentees</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($ktbGroup->members as $member)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                    <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $member->name }}
                                        @if($member->id === $ktbGroup->leader_id)
                                            <span class="ml-1 inline-flex rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">Leader</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">Gen {{ $member->generation }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($member->status === 'active')
                                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/20 dark:text-green-400">Active</span>
                                        @elseif($member->status === 'alumni')
                                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">Alumni</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/20 dark:text-red-400">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $member->mentees->count() }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('ktb-members.show', $member) }}" class="text-blue-600 hover:underline dark:text-blue-400">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-sm text-zinc-500 dark:text-zinc-400">Belum ada anggota di kelompok ini</p>
            @endif
        </div>
    </div>
</x-layouts.app>
