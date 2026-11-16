@extends('layouts.app')

@section('title', 'Tambah Tugas Magang Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header & Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition-colors mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Tugas
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Buat Tugas Magang Baru</h1>
        </div>
    </div>

    <!-- Educational Explanation Box -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 space-y-1.5">
        <p class="font-bold text-amber-950 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
            Konsep yang sedang dipelajari di Form ini:
        </p>
        <ul class="list-disc list-inside space-y-1 text-amber-800">
            <li><strong>@csrf Token:</strong> Wajib untuk mencegah serangan Cross-Site Request Forgery. Tanpa ini, form akan menghasilkan error <em>419 Page Expired</em>.</li>
            <li><strong>Form Request Validation:</strong> Data yang di-submit akan divalidasi oleh class <code class="bg-amber-100 px-1 py-0.5 rounded">TaskRequest</code>.</li>
            <li><strong>old('nama_field'):</strong> Fungsi penolong agar input yang sudah diketik user TIDAK HILANG saat validasi gagal.</li>
            <li><strong>@error('nama_field'):</strong> Directive Blade untuk menampilkan pesan error khusus pada input yang bermasalah.</li>
        </ul>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
            {{-- 1. CSRF TOKEN WAJIB --}}
            @csrf

            <!-- Field: Judul Tugas (title) -->
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">
                    Judul Tugas <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" id="title"
                       value="{{ old('title') }}"
                       placeholder="Contoh: Implementasi Routing & Controller"
                       class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-all
                              {{ $errors->has('title') ? 'border-rose-500 ring-2 ring-rose-200 bg-rose-50/30' : 'border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' }}">
                @error('title')
                    <p class="text-xs text-rose-600 font-medium mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </p>
                @enderror
                <span class="text-[11px] text-slate-400">Minimal 3 karakter, maksimal 150 karakter.</span>
            </div>

            <!-- Field: Deskripsi Tugas (description) -->
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">
                    Deskripsi Tugas <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                </label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Jelaskan detail instruksi pengerjaan tugas di sini..."
                          class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-all
                                 {{ $errors->has('description') ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid 2 Kolom: Prioritas & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Field: Prioritas (priority) -->
                <div>
                    <label for="priority" class="block text-sm font-semibold text-slate-700 mb-1">
                        Prioritas <span class="text-rose-500">*</span>
                    </label>
                    <select name="priority" id="priority"
                            class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-all
                                   {{ $errors->has('priority') ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' }}">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low (Rendah)</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium (Sedang)</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High (Tinggi/Penting)</option>
                    </select>
                    @error('priority')
                        <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field: Status (status) -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">
                        Status Awal <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status"
                            class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-all
                                   {{ $errors->has('status') ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' }}">
                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid 2 Kolom: Penanggung Jawab & Deadline -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Field: Penanggung Jawab (assigned_to) -->
                <div>
                    <label for="assigned_to" class="block text-sm font-semibold text-slate-700 mb-1">
                        Penanggung Jawab (PIC) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="assigned_to" id="assigned_to"
                           value="{{ old('assigned_to') }}"
                           placeholder="Contoh: Akmal / Andi"
                           class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-all
                                  {{ $errors->has('assigned_to') ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' }}">
                    @error('assigned_to')
                        <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field: Batas Waktu / Deadline (due_date) -->
                <div>
                    <label for="due_date" class="block text-sm font-semibold text-slate-700 mb-1">
                        Batas Waktu (Deadline) <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                    </label>
                    <input type="date" name="due_date" id="due_date"
                           value="{{ old('due_date') }}"
                           class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition-all
                                  {{ $errors->has('due_date') ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' }}">
                    @error('due_date')
                        <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('tasks.index') }}"
                   class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-md shadow-red-600/30 transition-all hover:scale-[1.02]">
                    Simpan Tugas (POST /tasks)
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
