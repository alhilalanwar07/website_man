{{-- Step 7: Konfirmasi & Review --}}
@php
    $prog1 = $programs->firstWhere('id', $pilihan_program_1_id);
    $prog2 = $programs->firstWhere('id', $pilihan_program_2_id);
    $prog3 = $programs->firstWhere('id', $pilihan_program_3_id);
    $trackSelected = $period->tracks->firstWhere('id', $track_id);
@endphp
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Konfirmasi Data</h2>
        <p class="mt-1 text-sm text-slate-500">Periksa kembali seluruh data sebelum dikirim.</p>
    </div>

    {{-- Data Pribadi --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Pribadi</span>
            <button type="button" wire:click="goToStep(2)" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ubah</button>
        </div>
        <div class="px-5 py-4 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div class="col-span-2"><span class="text-slate-400">Nama:</span> <strong>{{ $nama_lengkap }}</strong></div>
            <div><span class="text-slate-400">NISN:</span> {{ $nisn ?: '-' }}</div>
            <div><span class="text-slate-400">NIK:</span> {{ $nik ?: '-' }}</div>
            <div><span class="text-slate-400">TTL:</span> {{ $tempat_lahir }}, {{ $tanggal_lahir }}</div>
            <div><span class="text-slate-400">JK:</span> {{ $jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
            <div><span class="text-slate-400">Agama:</span> {{ $agama }}</div>
            <div><span class="text-slate-400">HP:</span> {{ $nomor_hp }}</div>
            <div class="col-span-2"><span class="text-slate-400">Email:</span> {{ $email }}</div>
        </div>
    </div>

    {{-- Sekolah & Jurusan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sekolah & Jurusan</span>
            <button type="button" wire:click="goToStep(3)" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ubah</button>
        </div>
        <div class="px-5 py-4 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div class="col-span-2"><span class="text-slate-400">Asal Sekolah:</span> <strong>{{ $asal_sekolah }}</strong></div>
            <div><span class="text-slate-400">Jalur:</span> {{ $trackSelected?->nama_jalur ?? '-' }}</div>
            <div><span class="text-slate-400">Jurusan 1:</span> <strong class="text-blue-700">{{ $prog1?->nama_jurusan ?? '-' }}</strong></div>
            <div><span class="text-slate-400">Jurusan 2:</span> {{ $prog2?->nama_jurusan ?? '-' }}</div>
            <div><span class="text-slate-400">Jurusan 3:</span> {{ $prog3?->nama_jurusan ?? '-' }}</div>
        </div>
    </div>

    {{-- Orang Tua --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data Orang Tua</span>
            <button type="button" wire:click="goToStep(4)" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ubah</button>
        </div>
        <div class="px-5 py-4 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div><span class="text-slate-400">Ayah:</span> {{ $nama_ayah }}</div>
            <div><span class="text-slate-400">TTL:</span> {{ $tempat_lahir_ayah }}, {{ $tanggal_lahir_ayah }}</div>
            <div><span class="text-slate-400">Pekerjaan:</span> {{ $pekerjaan_ayah }}</div>
            <div><span class="text-slate-400">HP Ayah:</span> {{ $nomor_hp_ayah ?: '-' }}</div>
            <div class="col-span-2 border-t border-slate-100 pt-2 mt-1"></div>
            <div><span class="text-slate-400">Ibu:</span> {{ $nama_ibu }}</div>
            <div><span class="text-slate-400">TTL:</span> {{ $tempat_lahir_ibu }}, {{ $tanggal_lahir_ibu }}</div>
            <div><span class="text-slate-400">Pekerjaan:</span> {{ $pekerjaan_ibu }}</div>
            <div><span class="text-slate-400">HP Ibu:</span> {{ $nomor_hp_ibu ?: '-' }}</div>
        </div>
    </div>

    {{-- Berkas --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Berkas</span>
            <button type="button" wire:click="goToStep(6)" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ubah</button>
        </div>
        <div class="px-5 py-4 flex flex-wrap gap-2">
            @php
                $berkas = [
                    'KK' => $file_kk,
                    'Akta' => $file_akta,
                    'Rapor Cover' => $file_rapor_cover,
                    'Nilai Rapor' => $file_rapor_nilai,
                    'Foto' => $file_pas_foto,
                    'Ijazah/SKL' => $file_skl,
                ];
            @endphp
            @foreach($berkas as $label => $file)
                <span @class([
                    'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold',
                    'bg-emerald-50 text-emerald-700' => $file,
                    'bg-red-50 text-red-400' => !$file,
                ])>
                    @if($file)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    @endif
                    {{ $label }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- Warning --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex gap-3">
        <div class="flex-shrink-0 w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-900">Pastikan data sudah benar</p>
            <p class="text-xs text-amber-700 mt-0.5">Setelah dikirim, data tidak dapat diubah sendiri. Hubungi panitia jika perlu revisi.</p>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center justify-between pt-2">
        <button type="button" wire:click="previousStep" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali
        </button>
        <button type="submit" wire:loading.attr="disabled" wire:target="submitApplication" class="inline-flex items-center gap-2 px-8 py-3.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-500/20 transition-all shadow-lg shadow-emerald-500/25 disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="submitApplication">Kirim Pendaftaran</span>
            <span wire:loading wire:target="submitApplication" class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Mengirim...
            </span>
        </button>
    </div>
</div>
