@extends('layouts.app')

@section('title', 'Detail Tugas: ' . $task->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition-colors mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Tugas
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Tugas #{{ $task->id }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tasks.edit', $task) }}"
               class="px-4 py-2 text-sm font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition-colors">
                ✏️ Edit Tugas
            </a>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus tugas ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-colors">
                    🗑️ Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-5">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $task->priority_badge }}">
                    Prioritas: {{ ucfirst($task->priority) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $task->status_badge }}">
                    Status: {{ $task->status_label }}
                </span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $task->title }}</h2>
        </div>

        <!-- Deskripsi -->
        <div>
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Instruksi Tugas</h3>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $task->description ?? 'Tidak ada catatan deskripsi tambahan untuk tugas ini.' }}
            </div>
        </div>

        <!-- Meta Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-xs text-slate-400 block">Penanggung Jawab (PIC)</span>
                <span class="text-sm font-semibold text-slate-800 mt-0.5 block">{{ $task->assigned_to }}</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-xs text-slate-400 block">Batas Waktu (Deadline)</span>
                <span class="text-sm font-semibold text-slate-800 mt-0.5 block">
                    {{ $task->due_date ? $task->due_date->translatedFormat('l, d F Y') : 'Tidak ditentukan' }}
                </span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-xs text-slate-400 block">Dibuat Pada</span>
                <span class="text-sm font-semibold text-slate-800 mt-0.5 block font-mono text-xs">
                    {{ $task->created_at->format('Y-m-d H:i:s') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Live Educational Debugger: Request & Response Inspector -->
    <div class="bg-slate-900 text-slate-200 rounded-2xl p-6 shadow-md border border-slate-800 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                <h3 class="font-bold text-sm text-white tracking-wide uppercase">Request & Response Inspector (Edukasi Magang)</h3>
            </div>
            <span class="text-xs text-slate-400 font-mono">Behind The Scenes</span>
        </div>

        <p class="text-xs text-slate-300">
            Berikut data riil yang sedang diproses Laravel di balik layar saat halaman ini dibuka:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-mono">
            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800 space-y-2">
                <p class="text-slate-400 font-sans font-semibold border-b border-slate-800 pb-1 text-emerald-400">
                    📥 HTTP Request Info:
                </p>
                <p><span class="text-slate-500">Method:</span> <strong class="text-yellow-400">{{ request()->method() }}</strong></p>
                <p><span class="text-slate-500">Full URL:</span> <span class="text-sky-300 break-all">{{ request()->fullUrl() }}</span></p>
                <p><span class="text-slate-500">Route Name:</span> <span class="text-purple-300">{{ Route::currentRouteName() }}</span></p>
                <p><span class="text-slate-500">Client IP:</span> {{ request()->ip() }}</p>
            </div>

            <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800 space-y-2">
                <p class="text-slate-400 font-sans font-semibold border-b border-slate-800 pb-1 text-sky-400">
                    📦 Route Model Binding & Eloquent:
                </p>
                <p><span class="text-slate-500">Model Class:</span> App\Models\Task</p>
                <p><span class="text-slate-500">Resolved ID:</span> {{ $task->id }}</p>
                <p><span class="text-slate-500">Database Table:</span> tasks</p>
                <p><span class="text-slate-500">SQL Ekivalen:</span> <span class="text-slate-300">SELECT * FROM tasks WHERE id = {{ $task->id }} LIMIT 1</span></p>
            </div>
        </div>

        <div>
            <span class="text-[11px] text-slate-400 block mb-1">Raw JSON Model Data ($task->toJson()):</span>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] text-emerald-400 overflow-x-auto">{{ json_encode($task->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>

</div>
@endsection
