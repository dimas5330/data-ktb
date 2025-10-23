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

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
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
</x-layouts.app>
