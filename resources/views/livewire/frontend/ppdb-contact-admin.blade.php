<div>
    <section class="relative overflow-hidden bg-slate-950 text-white py-24 noise">
        <div class="absolute inset-0 bg-mesh-hero opacity-70"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex px-4 py-1.5 rounded-full glass text-xs font-bold uppercase tracking-[0.3em] text-blue-300 mb-6">Layanan PPDB</span>
            <h1 class="text-4xl lg:text-6xl font-black tracking-tight leading-tight">Hubungi <span class="text-gradient">Admin PPDB</span></h1>
            <p class="text-slate-300 max-w-2xl mx-auto mt-4">Jika sudah daftar tapi ingin ubah data, silakan hubungi admin melalui WhatsApp atau email resmi PPDB.</p>
        </div>
    </section>

    @include('livewire.frontend.partials.ppdb-journey-nav', [
        'active' => 'contact',
    ])

    <section class="py-20 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm p-8 md:p-10 space-y-6">
                <div class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4 text-blue-800">
                    <p class="font-semibold">Jika sudah daftar tapi ingin ubah data, silakan hubungi admin.</p>
                    <p class="text-sm mt-1">Sertakan nomor pendaftaran dan data yang ingin diperbaiki agar proses lebih cepat.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">WhatsApp Admin</p>
                        @if($whatsappLink)
                            <p class="mt-3 text-sm text-slate-600">Klik tombol di bawah untuk langsung chat admin PPDB.</p>
                            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="mt-4 inline-flex rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700">Hubungi via WhatsApp</a>
                        @else
                            <p class="mt-3 text-sm text-amber-700">Nomor WhatsApp admin belum dikonfigurasi.</p>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Email Admin</p>
                        @if($mailtoLink)
                            <p class="mt-3 text-sm text-slate-600">Kirim email resmi ke admin PPDB sekolah.</p>
                            <a href="{{ $mailtoLink }}" class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">Kirim Email</a>
                            <p class="mt-3 text-xs text-slate-500">Alamat email: {{ $adminEmail }}</p>
                        @else
                            <p class="mt-3 text-sm text-amber-700">Email admin belum dikonfigurasi.</p>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-700">
                    <p class="font-semibold">Format pesan yang disarankan:</p>
                    <ul class="mt-2 list-disc pl-5 space-y-1 text-slate-600">
                        <li>Nomor pendaftaran</li>
                        <li>Nama lengkap pendaftar</li>
                        <li>Data yang ingin diubah</li>
                        <li>Alasan perubahan data</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
