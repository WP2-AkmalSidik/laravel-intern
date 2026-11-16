@extends('layouts.app')

@section('title', 'Daftar Tugas Magang')

@section('content')
<div class="space-y-6">

    <!-- Header & Educational Concept Box -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/30 mb-2">
                    MVC: View (tasks/index.blade.php) & Controller (TaskController@index)
                </span>
                <h1 class="text-2xl font-bold tracking-tight">Manajemen Tugas Magang</h1>
                <p class="text-slate-300 text-sm mt-1 max-w-2xl">
                    Halaman ini menampilkan data dari database via <strong>Eloquent ORM</strong> (<code class="text-red-300">Task::latest()->paginate(5)</code>)
                    yang dikirim oleh <strong>Controller</strong> ke <strong>View Blade</strong> ini.
                </p>
            </div>
            <a href="{{ route('tasks.create') }}"
               class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-md shadow-red-600/30 transition-all hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Tugas Baru
            </a>
        </div>
    </div>

    <!-- Statistics Cards (Eloquent Aggregate Queries) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Total Tugas</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
            <span class="text-[11px] text-slate-400">Task::count()</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Menunggu (Pending)</p>
            <p class="text-2xl font-bold text-slate-700 mt-1">{{ $stats['pending'] }}</p>
            <span class="text-[11px] text-slate-400">Task::pending()->count()</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Sedang Dikerjakan</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['in_progress'] }}</p>
            <span class="text-[11px] text-slate-400">status = 'in_progress'</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Selesai</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
            <span class="text-[11px] text-slate-400">Task::completed()->count()</span>
        </div>
    </div>

    <!-- Filter & Search (Contoh HTTP GET Request via Query String) -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <div class="flex-1 w-full flex flex-col sm:flex-row gap-3">
                <!-- Search input -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $searchKeyword }}" placeholder="Cari judul tugas atau nama PIC..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="in_progress" {{ $statusFilter === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg transition-colors">
                    Filter Data
                </button>
                @if ($searchKeyword || $statusFilter)
                    <a href="{{ route('tasks.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table (Eloquent Collection to HTML View) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">ID</th>
                        <th class="px-4 py-3.5">Judul & Deskripsi Tugas</th>
                        <th class="px-4 py-3.5">Prioritas</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">PIC (Assigned To)</th>
                        <th class="px-4 py-3.5">Deadline</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 text-center font-mono text-xs text-slate-400">
                                #{{ $task->id }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-red-600 transition-colors">
                                    {{ $task->title }}
                                </a>
                                <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">
                                    {{ $task->description ?? 'Tidak ada deskripsi tambahan.' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $task->priority_badge }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $task->status_badge }}">
                                    {{ $task->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-800">
                                {{ $task->assigned_to }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Tombol Detail (GET /tasks/{task}) -->
                                    <a href="{{ route('tasks.show', $task) }}" title="Lihat Detail"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <!-- Tombol Edit (GET /tasks/{task}/edit) -->
                                    <a href="{{ route('tasks.edit', $task) }}" title="Edit Tugas"
                                       class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <!-- Tombol Hapus (DELETE /tasks/{task}) dengan CSRF & Method Spoofing -->
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                          onsubmit="return confirm('Apakah kamu yakin ingin menghapus tugas ini? (Akan dieksekusi method DELETE)');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Tugas"
                                                class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="font-medium text-slate-600">Belum ada data tugas</p>
                                    <p class="text-xs text-slate-400 mt-1">Silakan klik "Tambah Tugas Baru" atau reset filter pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $tasks->links() }}
        </div>
    </div>

</div>
@endsection
