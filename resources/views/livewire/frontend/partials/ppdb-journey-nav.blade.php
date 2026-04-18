@php
    $active = $active ?? 'overview';
    $periodQuery = $periodQuery ?? [];

    $steps = [
        [
            'key' => 'overview',
            'label' => 'Informasi',
            'desc' => 'Pelajari jadwal, jalur, dan syarat dokumen.',
            'href' => route('ppdb.index', $periodQuery),
            'number' => '01',
        ],
        [
            'key' => 'form',
            'label' => 'Pendaftaran',
            'desc' => 'Isi formulir lengkap dan kirim berkas.',
            'href' => route('ppdb.form', $periodQuery),
            'number' => '02',
        ],
        [
            'key' => 'status',
            'label' => 'Cek Status',
            'desc' => 'Pantau progres verifikasi dan hasil.',
            'href' => route('ppdb.status'),
            'number' => '03',
        ],
        [
            'key' => 'reregistration',
            'label' => 'Daftar Ulang',
            'desc' => 'Konfirmasi ulang bila sudah lulus resmi.',
            'href' => route('ppdb.daftar-ulang'),
            'number' => '04',
        ],
        [
            'key' => 'contact',
            'label' => 'Hubungi Admin',
            'desc' => 'Ajukan bantuan perubahan data pendaftaran.',
            'href' => route('ppdb.contact'),
            'number' => '05',
        ],
    ];
@endphp

<section class="py-8 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 flex-wrap mb-4">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Alur Cepat PPDB</p>
            <p class="text-xs text-slate-500">Ikuti urutan langkah agar proses lebih mudah dipahami.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
            @foreach ($steps as $step)
                @php($isActive = $active === $step['key'])
                <a
                    wire:key="ppdb-journey-{{ $step['key'] }}"
                    href="{{ $step['href'] }}"
                    class="group rounded-2xl border px-4 py-4 transition {{ $isActive ? 'border-blue-200 bg-blue-50 shadow-sm' : 'border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-white' }}"
                >
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-black {{ $isActive ? 'text-blue-700' : 'text-slate-900' }}">{{ $step['label'] }}</p>
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold tracking-wider {{ $isActive ? 'bg-blue-100 text-blue-700' : 'bg-slate-200 text-slate-600' }}">{{ $step['number'] }}</span>
                    </div>
                    <p class="mt-2 text-xs leading-relaxed {{ $isActive ? 'text-blue-700/90' : 'text-slate-500' }}">{{ $step['desc'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
