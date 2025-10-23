<x-layouts.app title="Kelompok KTB">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Kelompok KTB</h1>
            <a href="{{ route('ktb-groups.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Tambah Kelompok
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Nama Kelompok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Pemimpin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Jumlah Anggota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($groups as $group)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $group->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $group->leader?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $group->members_count }} anggota
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $group->location ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($group->status === 'active')
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/20 dark:text-green-400">Active</span>
                                @elseif($group->status === 'completed')
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">Completed</span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/20 dark:text-red-400">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('ktb-groups.show', $group) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">Detail</a>
                                    <a href="{{ route('ktb-groups.assign-members', $group) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400">Assign</a>
                                    <a href="{{ route('ktb-groups.edit', $group) }}" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400">Edit</a>
                                    <form action="{{ route('ktb-groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kelompok ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                Belum ada kelompok KTB
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $groups->links() }}
        </div>
    </div>
</x-layouts.app>
