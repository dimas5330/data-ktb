<x-layouts.app title="Daftar Anggota KTB">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Daftar Anggota KTB</h1>
            <a href="{{ route('ktb-members.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Anggota
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-zinc-900 dark:text-white">ID</th>
                            <th class="px-6 py-3 font-semibold text-zinc-900 dark:text-white">Nama</th>
                            <th class="px-6 py-3 font-semibold text-zinc-900 dark:text-white">Kelompok</th>
                            <th class="px-6 py-3 font-semibold text-zinc-900 dark:text-white">Generation</th>
                            <th class="px-6 py-3 font-semibold text-zinc-900 dark:text-white">Status</th>
                            <th class="px-6 py-3 font-semibold text-zinc-900 dark:text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($members as $member)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 text-zinc-900 dark:text-white">{{ $member->id }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('ktb-members.show', $member) }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400">
                                    {{ $member->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $member->currentGroup?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300">
                                    Gen {{ $member->generation }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($member->status === 'active')
                                    <span class="inline-flex rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                @elseif($member->status === 'alumni')
                                    <span class="inline-flex rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Alumni</span>
                                @else
                                    <span class="inline-flex rounded-md bg-red-100 px-2 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('ktb-members.edit', $member) }}" class="inline-flex items-center rounded-md bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600">
                                        Edit
                                    </a>
                                    <form action="{{ route('ktb-members.destroy', $member) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus anggota?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                {{ $members->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
