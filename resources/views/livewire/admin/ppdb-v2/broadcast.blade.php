<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Broadcast & Pengumuman</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kirim otomatis pengingat jadwal langsung ke WhatsApp tau Email peserta.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 p-4 text-blue-800 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-400 font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Kirim Pesan -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Buat Pesan Massal</h3>
            
            <form wire:submit.prevent="sendBroadcast" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Target Penerima</label>
                    <select wire:model="targetAudience" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50 dark:bg-slate-950 dark:border-slate-700">
                        <option value="all">Semua Pendaftar Terdaftar</option>
                        <option value="verified">Siswa Terverifikasi Berkas (Tunggu Ujian)</option>
                        <option value="lulus">Siswa Lulus (Semua Jurusan)</option>
                        <option value="belum_daftar_ulang">Siswa Lulus (Belum Daftar Ulang)</option>
                        <option value="selesai_daftar_ulang">Siswa Lulus (Selesai Daftar Ulang)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Jalur Pengiriman</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer border rounded-xl p-3 flex-1 transition {{ $channel === 'whatsapp' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/20' : 'border-slate-200' }}">
                            <input type="radio" wire:model.live="channel" value="whatsapp" class="text-emerald-500 focus:ring-emerald-500">
                            <x-admin.icon name="chat" class="w-5 h-5 text-emerald-500" />
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">WhatsApp</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer border rounded-xl p-3 flex-1 transition {{ $channel === 'email' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-slate-200' }}">
                            <input type="radio" wire:model.live="channel" value="email" class="text-blue-500 focus:ring-blue-500">
                            <x-admin.icon name="mail" class="w-5 h-5 text-blue-500" />
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Email Utama</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Isi Pesan/Pengumuman</label>
                    <textarea wire:model="messageText" rows="5" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-inner bg-slate-50 dark:bg-slate-950 dark:border-slate-700" placeholder="Tulis pengingat jadwal, tes, atau pengumuman seragam di sini..."></textarea>
                    <p class="text-xs text-slate-500 mt-1">Anda dapat menggunakan variabel otomatis seperti [nama_siswa] atau [no_daftar] agar terpersonalisasi.</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                        <x-admin.icon name="paper-airplane" class="w-5 h-5" />
                        Arahkan ke Antrean Kirim (Queue)
                    </button>
                    <div wire:loading wire:target="sendBroadcast" class="w-full text-center text-sm font-bold text-blue-600 mt-2">
                        Memproses pengiriman...
                    </div>
                </div>
            </form>
        </div>

        <!-- Panduan & Pengaturan Teks Default -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-blue-50 p-6 shadow-sm dark:border-slate-800 dark:bg-blue-900/20">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-800 p-2 rounded-full mt-1">
                        <x-admin.icon name="information-circle" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-900 dark:text-blue-100">Kirim Otomatis</h4>
                        <p class="text-sm text-blue-800/80 dark:text-blue-200/80 mt-1 leading-relaxed">
                            Selain menge-blast pesan manual di layar ini, sistem PPDB juga sudah mengaktifkan **notifikasi otomatis** saat Anda menaruh siswa ke jurusan tertentu di menu Penentuan Jurusan. Tidak perlu manual!
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm">Pengumuman di Web Pendaftar</h3>
                </div>
                <div class="p-5 text-center">
                    <p class="text-sm text-slate-500 mb-4">Fitur ini menulis banner berjalan langsung di website PPDB bagi peserta yang tidak mendaftar lewat WA/Email aktif.</p>
                    <button class="px-5 py-2 rounded-xl border border-slate-300 text-sm font-bold hover:bg-slate-50 transition dark:border-slate-700 dark:hover:bg-slate-800">
                        Tambahkan Banner Pengumuman
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
