<?php

namespace App\Livewire\Admin\PpdbV2;

use Illuminate\Validation\Rule;
use Livewire\Component;

class Broadcast extends Component
{
    private const DEFAULT_AUDIENCE = 'all';
    private const DEFAULT_CHANNEL = 'whatsapp';

    private const AUDIENCE_GUIDE = [
        'all' => 'Untuk informasi umum: pembukaan pendaftaran, perubahan jadwal besar, dan ketentuan dokumen.',
        'verified' => 'Untuk kandidat siap tes/seleksi lanjutan setelah verifikasi berkas selesai.',
        'lulus' => 'Untuk komunikasi pasca-kelulusan: pengumuman resmi, instruksi daftar ulang, dan orientasi.',
        'belum_daftar_ulang' => 'Untuk reminder deadline daftar ulang serta berkas wajib yang belum lengkap.',
        'selesai_daftar_ulang' => 'Untuk penguatan informasi akhir: daftar ulang valid, jadwal masuk, dan agenda awal.',
    ];

    private const TOKEN_GUIDE = [
        ['token' => '[nama_siswa]', 'desc' => 'Nama lengkap peserta'],
        ['token' => '[no_daftar]', 'desc' => 'Nomor pendaftaran'],
        ['token' => '[jurusan]', 'desc' => 'Jurusan hasil seleksi'],
        ['token' => '[tanggal]', 'desc' => 'Tanggal kegiatan'],
    ];

    private const QUICK_TEMPLATES = [
        [
            'key' => 'template-jadwal-tes',
            'title' => 'Jadwal Tes',
            'audience' => 'verified',
            'channel' => 'whatsapp',
            'body' => 'Halo [nama_siswa] ([no_daftar]), Anda dijadwalkan mengikuti tes PPDB pada [tanggal]. Mohon hadir 30 menit lebih awal dengan membawa kartu peserta.',
        ],
        [
            'key' => 'template-hasil-lulus',
            'title' => 'Pengumuman Lulus',
            'audience' => 'lulus',
            'channel' => 'email',
            'body' => 'Selamat [nama_siswa], Anda dinyatakan LULUS di jurusan [jurusan]. Segera lakukan daftar ulang sebelum batas waktu [tanggal].',
        ],
        [
            'key' => 'template-reminder-daful',
            'title' => 'Reminder Daftar Ulang',
            'audience' => 'belum_daftar_ulang',
            'channel' => 'whatsapp',
            'body' => 'Pengingat untuk [nama_siswa] ([no_daftar]): daftar ulang PPDB belum tuntas. Silakan lengkapi proses sebelum [tanggal] agar status tetap aktif.',
        ],
        [
            'key' => 'template-info-umum',
            'title' => 'Informasi Umum',
            'audience' => 'all',
            'channel' => 'whatsapp',
            'body' => 'Pengumuman PPDB: mohon cek pembaruan jadwal dan ketentuan berkas terbaru di portal resmi sekolah. Info ini berlaku untuk seluruh peserta.',
        ],
    ];

    private const CHECKLIST_ITEMS = [
        'Pastikan target penerima sesuai tahap proses PPDB saat ini.',
        'Gunakan bahasa instruktif dan cantumkan batas waktu yang jelas.',
        'Sertakan token personalisasi agar peserta lebih mudah mengenali pesan.',
        'Kirim ulang hanya untuk grup yang belum menindaklanjuti.',
        'Simpan arsip konten untuk audit komunikasi periode berjalan.',
    ];

    public string $targetAudience = self::DEFAULT_AUDIENCE;
    public string $channel = self::DEFAULT_CHANNEL;
    public string $messageText = '';

    public function mount(): void
    {
        $this->targetAudience = $this->normalizeAudience($this->targetAudience);
        $this->channel = $this->normalizeChannel($this->channel);
    }

    public function applyTemplate(string $templateKey): void
    {
        $template = $this->templateByKey($templateKey);

        if ($template === null) {
            return;
        }

        $this->targetAudience = $this->normalizeAudience((string) $template['audience']);
        $this->channel = $this->normalizeChannel((string) $template['channel']);
        $this->messageText = (string) $template['body'];
    }

    public function sendBroadcast(): void
    {
        $validated = $this->validate();
        $this->messageText = trim((string) $validated['messageText']);

        // Logic pengiriman nyata bisa dihubungkan ke API gateway / mail queue.
        session()->flash('message', 'Broadcast telah dimasukkan ke dalam antrean (Queue) untuk segera dikirimkan via ' . strtoupper($this->channel) . ' ke partisipan terpilih.');

        $this->reset('messageText');
    }

    public function render()
    {
        $periodQuery = $this->periodQuery();

        return view('livewire.admin.ppdb-v2.broadcast', [
            'audienceGuide' => self::AUDIENCE_GUIDE,
            'tokenGuide' => self::TOKEN_GUIDE,
            'quickTemplates' => self::QUICK_TEMPLATES,
            'integrationMap' => $this->integrationMap($periodQuery),
            'checklistItems' => self::CHECKLIST_ITEMS,
            'previewText' => $this->previewText(),
        ])->layout('components.layouts.admin', ['title' => 'Broadcast & Pengumuman']);
    }

    protected function rules(): array
    {
        return [
            'targetAudience' => ['required', Rule::in(array_keys(self::AUDIENCE_GUIDE))],
            'channel' => ['required', Rule::in(['whatsapp', 'email'])],
            'messageText' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    protected function periodQuery(): array
    {
        $period = request()->query('periode');

        return is_scalar($period) && $period !== ''
            ? ['periode' => (string) $period]
            : [];
    }

    protected function integrationMap(array $periodQuery): array
    {
        return [
            ['menu' => 'Dashboard', 'goal' => 'Pantau KPI komunikasi', 'route' => route('admin.ppdb.dashboard', $periodQuery)],
            ['menu' => 'Pendaftar', 'goal' => 'Validasi data target sebelum blast', 'route' => route('admin.ppdb.pendaftar', $periodQuery)],
            ['menu' => 'Penentuan Jurusan', 'goal' => 'Follow-up pasca penempatan jurusan', 'route' => route('admin.ppdb.penentuan-jurusan', $periodQuery)],
            ['menu' => 'Daftar Ulang', 'goal' => 'Reminder progres daftar ulang', 'route' => route('admin.ppdb.daftar-ulang', $periodQuery)],
            ['menu' => 'Pengaturan', 'goal' => 'Sinkronkan isi pesan dengan timeline periode', 'route' => route('admin.ppdb.pengaturan', $periodQuery)],
        ];
    }

    protected function previewText(): string
    {
        $message = trim($this->messageText);

        if ($message === '') {
            return 'Pratinjau akan muncul setelah Anda menulis isi pesan.';
        }

        return str_replace(
            ['[nama_siswa]', '[no_daftar]', '[jurusan]', '[tanggal]'],
            ['Alya Nur Aini', 'PPDB-260142', 'Teknik Komputer dan Jaringan', now()->format('d M Y')],
            $message
        );
    }

    protected function templateByKey(string $templateKey): ?array
    {
        foreach (self::QUICK_TEMPLATES as $template) {
            if (($template['key'] ?? null) === $templateKey) {
                return $template;
            }
        }

        return null;
    }

    protected function normalizeAudience(string $audience): string
    {
        return array_key_exists($audience, self::AUDIENCE_GUIDE)
            ? $audience
            : self::DEFAULT_AUDIENCE;
    }

    protected function normalizeChannel(string $channel): string
    {
        return in_array($channel, ['whatsapp', 'email'], true)
            ? $channel
            : self::DEFAULT_CHANNEL;
    }
}
