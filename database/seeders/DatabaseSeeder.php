<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\GaleriAlbum;
use App\Models\GaleriItem;
use App\Models\KategoriBerita;
use App\Models\Pegawai;
use App\Models\PpdbApplication;
use App\Models\PpdbDocument;
use App\Models\PpdbPeriod;
use App\Models\PpdbQuota;
use App\Models\PpdbTrack;
use App\Models\Pengumuman;
use App\Models\ProfilSekolah;
use App\Models\ProgramKeahlian;
use App\Models\Role;
use App\Models\Setting;
use App\Models\EkstrakurikulerKategori;
use App\Models\Ekstrakurikuler;
use App\Models\User;
use App\Support\PpdbSelectionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles & Admin ──────────────────────────────
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $ppdbAdminRole = Role::create(['name' => 'ppdb-admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@man2kolaka.sch.id',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $admin->roles()->attach($adminRole);

        $editor = User::create([
            'name' => 'Editor Konten',
            'email' => 'editor@man2kolaka.sch.id',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $editor->roles()->attach($editorRole);

        $ppdbAdmin = User::create([
            'name' => 'Admin PPDB',
            'email' => 'ppdb@man2kolaka.sch.id',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $ppdbAdmin->roles()->attach($ppdbAdminRole);

        // ── Profil Sekolah ─────────────────────────────
        ProfilSekolah::create([
            'npsn' => '40402345',
            'nama_sekolah' => 'MAN 2 Kolaka',
            'alamat_lengkap' => 'Jl. Pemuda No. 12, Kelurahan Laloeha, Kecamatan Kolaka, Kabupaten Kolaka, Sulawesi Tenggara 93511',
            'koordinat_peta' => '-4.0454,121.5906',
            'nomor_telepon' => '(0405) 21234',
            'email_resmi' => 'info@man2kolaka.sch.id',
            'tautan_sosmed' => [
                'facebook' => 'https://facebook.com/man2kolaka',
                'instagram' => 'https://instagram.com/man2kolaka',
                'youtube' => 'https://youtube.com/@man2kolaka',
                'tiktok' => 'https://tiktok.com/@man2kolaka',
            ],
            'teks_sambutan_kepsek' => 'Assalamu\'alaikum Warahmatullahi Wabarakatuh. Puji syukur kita panjatkan ke hadirat Allah SWT atas segala limpahan rahmat dan karunia-Nya. Selamat datang di website resmi MAN 2 Kolaka, madrasah unggulan yang terus berkomitmen membina generasi berakhlak, berprestasi, dan siap menghadapi tantangan zaman. Kami percaya bahwa setiap siswa memiliki potensi luar biasa yang perlu digali dan dikembangkan. Melalui kurikulum terpadu, fasilitas modern, serta tenaga pendidik yang profesional, kami siap menjadi mitra terbaik dalam mewujudkan cita-cita generasi muda Kolaka. Mari bersama-sama kita wujudkan pendidikan madrasah berkualitas untuk Indonesia yang lebih maju.',
            'visi_teks' => 'Menjadi lembaga pendidikan madrasah unggulan yang menghasilkan lulusan beriman, berkarakter, kompeten, dan berdaya saing global pada tahun 2030.',
            'misi_teks' => "1. Menyelenggarakan pendidikan berbasis kompetensi dan karakter sesuai kebutuhan masyarakat.\n2. Membentuk peserta didik yang beriman, bertakwa, dan berakhlak mulia.\n3. Mengembangkan kurikulum yang adaptif terhadap perkembangan ilmu pengetahuan dan teknologi.\n4. Meningkatkan kerja sama dengan dunia usaha, perguruan tinggi, dan masyarakat.\n5. Mengoptimalkan penggunaan teknologi informasi dalam proses pembelajaran dan manajemen madrasah.\n6. Mengembangkan budaya mutu, inovasi, dan kepedulian sosial di lingkungan madrasah.\n7. Menyiapkan lulusan yang mampu bersaing di tingkat nasional dan internasional.",
        ]);

        // ── Settings ───────────────────────────────────
        $settings = [
            ['key' => 'site_name', 'value' => 'MAN 2 Kolaka', 'type' => 'string'],
            ['key' => 'site_tagline', 'value' => 'Madrasah Unggul Berprestasi', 'type' => 'string'],
            ['key' => 'footer_text', 'value' => '© 2026 MAN 2 Kolaka. All rights reserved.', 'type' => 'string'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean'],
        ];
        foreach ($settings as $s) {
            Setting::create($s);
        }

        // ── Program Keahlian (Peminatan MAN) ─────────
        $jurusans = [
            [
                'kode_jurusan' => 'IPA',
                'nama_jurusan' => 'Ilmu Pengetahuan Alam',
                'deskripsi_lengkap' => '<p>Peminatan Ilmu Pengetahuan Alam (IPA) dirancang untuk siswa yang memiliki minat dan bakat di bidang sains, matematika, dan teknologi. Siswa akan mendalami mata pelajaran Matematika, Fisika, Kimia, dan Biologi secara intensif dengan pendekatan berbasis eksperimen dan penelitian ilmiah.</p><p>Lulusan peminatan IPA memiliki peluang luas untuk melanjutkan ke perguruan tinggi di bidang kedokteran, teknik, farmasi, pertanian, dan berbagai program sains terapan lainnya.</p>',
                'fasilitas_unggulan' => "Laboratorium Fisika lengkap\nLaboratorium Kimia berstandar keamanan\nLaboratorium Biologi dengan mikroskop digital\nRuang belajar ber-AC\nPerpustakaan sains & referensi ilmiah",
                'prospek_karir' => "Dokter / Tenaga Medis\nFarmasist\nInsinyur / Teknisi\nPeneliti Sains\nDosen / Akademisi\nProgrammer & Data Scientist\nAhli Lingkungan Hidup",
            ],
            [
                'kode_jurusan' => 'IPS',
                'nama_jurusan' => 'Ilmu Pengetahuan Sosial',
                'deskripsi_lengkap' => '<p>Peminatan Ilmu Pengetahuan Sosial (IPS) membekali siswa dengan pemahaman mendalam tentang dinamika sosial, ekonomi, geografi, dan sejarah. Siswa dilatih berpikir kritis, analitis, dan komunikatif dalam memahami fenomena sosial di sekitarnya.</p><p>Peminatan ini membuka peluang luas di bidang hukum, ekonomi, bisnis, ilmu sosial, dan pemerintahan. Siswa juga dibekali keterampilan literasi keuangan dan kewirausahaan.</p>',
                'fasilitas_unggulan' => "Ruang diskusi & debat\nLab komputer untuk analisis data sosial\nPerpustakaan ilmu sosial & humaniora\nRuang belajar ber-AC\nAkses jurnal dan referensi online",
                'prospek_karir' => "Ekonom / Analis Keuangan\nPengacara / Notaris\nPegawai Negeri Sipil\nWirausahawan\nJurnalis / Penulis\nDiplomat / Hubungan Internasional\nAktivis Sosial & LSM",
            ],
            [
                'kode_jurusan' => 'BBK',
                'nama_jurusan' => 'Bahasa dan Budaya',
                'deskripsi_lengkap' => '<p>Peminatan Bahasa dan Budaya mengembangkan kemampuan linguistik, sastra, dan apresiasi budaya siswa secara komprehensif. Siswa mendalami Bahasa Indonesia, Bahasa Inggris, Bahasa Arab, serta sastra dan seni budaya Nusantara.</p><p>Peminatan ini sangat cocok bagi siswa yang memiliki minat di bidang komunikasi, sastra, jurnalistik, dan diplomasi budaya. Lulusan siap melanjutkan ke perguruan tinggi di bidang bahasa, sastra, komunikasi, dan hubungan internasional.</p>',
                'fasilitas_unggulan' => "Lab Bahasa multimedia\nStudio rekaman mini untuk praktik pidato\nPerpustakaan sastra & budaya\nRuang seni dan pertunjukan\nAkses kamus & referensi bahasa digital",
                'prospek_karir' => "Penerjemah / Interpreter\nJurnalis & Penulis\nGuru Bahasa\nDiplomat\nPenyiar Radio / TV\nEditor & Konten Kreator\nPegawai Kedutaan Besar",
            ],
            [
                'kode_jurusan' => 'KAG',
                'nama_jurusan' => 'Keagamaan',
                'deskripsi_lengkap' => '<p>Peminatan Keagamaan merupakan keunggulan khas Madrasah Aliyah yang memadukan ilmu agama Islam secara mendalam dengan ilmu pengetahuan umum. Siswa mendalami Al-Qur\'an Hadits, Fiqih, Aqidah Akhlak, Sejarah Kebudayaan Islam, dan Bahasa Arab tingkat lanjut.</p><p>Peminatan ini mencetak generasi yang tidak hanya cerdas secara intelektual, tetapi juga kuat dalam iman, akhlak, dan kepemimpinan berbasis nilai-nilai Islam. Lulusan siap melanjutkan ke perguruan tinggi agama Islam maupun umum.</p>',
                'fasilitas_unggulan' => "Masjid madrasah yang representatif\nLab Tahfidz Al-Qur\'an\nPerpustakaan kitab kuning & referensi Islam\nRuang kajian & diskusi keagamaan\nProgram tahfidz & halaqah rutin",
                'prospek_karir' => "Ulama / Da\'i\nGuru Agama Islam\nPegawai Kementerian Agama\nHakim Pengadilan Agama\nPenghulu / KUA\nAktivis Organisasi Islam\nDosen Perguruan Tinggi Agama",
            ],
        ];

        $programIds = [];
        foreach ($jurusans as $j) {
            $pk = ProgramKeahlian::create([
                'kode_jurusan' => $j['kode_jurusan'],
                'nama_jurusan' => $j['nama_jurusan'],
                'slug' => Str::slug($j['nama_jurusan']),
                'deskripsi_lengkap' => $j['deskripsi_lengkap'],
                'fasilitas_unggulan' => $j['fasilitas_unggulan'],
                'prospek_karir' => $j['prospek_karir'],
                'status_tampil' => true,
            ]);
            $programIds[$j['kode_jurusan']] = $pk->id;
        }

        // ── PPDB Foundation ──────────────────────────
        $quotaBlueprint = [
            'IPA'  => ['reguler' => 54, 'prestasi' => 18, 'afirmasi' => 9],
            'IPS'  => ['reguler' => 54, 'prestasi' => 18, 'afirmasi' => 9],
            'BBK'  => ['reguler' => 27, 'prestasi' => 9,  'afirmasi' => 4],
            'KAG'  => ['reguler' => 27, 'prestasi' => 9,  'afirmasi' => 4],
        ];

        $createTracksAndQuotas = function (PpdbPeriod $period) use ($programIds, $quotaBlueprint) {
            $tracks = [
                'reguler' => PpdbTrack::create([
                    'period_id' => $period->id,
                    'nama_jalur' => 'Jalur Reguler',
                    'slug' => 'reguler',
                    'deskripsi' => 'Jalur umum berbasis nilai rapor, kelengkapan berkas, dan verifikasi panitia.',
                    'urutan' => 1,
                ]),
                'prestasi' => PpdbTrack::create([
                    'period_id' => $period->id,
                    'nama_jalur' => 'Jalur Prestasi',
                    'slug' => 'prestasi',
                    'deskripsi' => 'Jalur khusus untuk calon siswa dengan prestasi akademik atau non-akademik.',
                    'urutan' => 2,
                ]),
                'afirmasi' => PpdbTrack::create([
                    'period_id' => $period->id,
                    'nama_jalur' => 'Jalur Afirmasi',
                    'slug' => 'afirmasi',
                    'deskripsi' => 'Jalur afirmasi dengan verifikasi dokumen pendukung sesuai kebijakan sekolah.',
                    'urutan' => 3,
                ]),
            ];

            foreach ($quotaBlueprint as $kode => $items) {
                foreach ($items as $trackKey => $kuota) {
                    PpdbQuota::create([
                        'period_id' => $period->id,
                        'track_id' => $tracks[$trackKey]->id,
                        'program_keahlian_id' => $programIds[$kode],
                        'kuota' => $kuota,
                        'kuota_terisi' => 0,
                        'status_aktif' => true,
                    ]);
                }
            }

            return $tracks;
        };

        $archivedPeriod = PpdbPeriod::create([
            'nama_periode' => 'PPDB Gelombang 1 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'gelombang_ke' => 1,
            'gelombang_label' => 'Gelombang 1',
            'tanggal_mulai_pendaftaran' => now()->subMonths(3)->toDateString(),
            'tanggal_selesai_pendaftaran' => now()->subMonths(2)->toDateString(),
            'tanggal_pengumuman' => now()->subMonths(2)->addDays(7)->toDateString(),
            'tanggal_mulai_daftar_ulang' => now()->subMonths(2)->addDays(8)->toDateString(),
            'tanggal_selesai_daftar_ulang' => now()->subMonths(2)->addDays(15)->toDateString(),
            'deskripsi' => 'Gelombang awal PPDB tahun ajaran 2026/2027.',
            'status' => 'archived',
            'status_pengumuman' => 'published',
            'hasil_diumumkan_at' => now()->subMonths(2),
            'catatan_pengumuman' => 'Arsip hasil gelombang pertama.',
            'is_active' => false,
        ]);

        $createTracksAndQuotas($archivedPeriod);

        $period = PpdbPeriod::create([
            'nama_periode' => 'PPDB Gelombang 2 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'gelombang_ke' => 2,
            'gelombang_label' => 'Gelombang 2',
            'tanggal_mulai_pendaftaran' => now()->subDays(5)->toDateString(),
            'tanggal_selesai_pendaftaran' => now()->addDays(60)->toDateString(),
            'tanggal_pengumuman' => now()->addDays(75)->toDateString(),
            'tanggal_mulai_daftar_ulang' => now()->subDay()->toDateString(),
            'tanggal_selesai_daftar_ulang' => now()->addDays(7)->toDateString(),
            'deskripsi' => 'Gelombang lanjutan PPDB untuk calon peserta didik baru MAN 2 Kolaka tahun ajaran 2026/2027.',
            'status' => 'published',
            'status_pengumuman' => 'published',
            'hasil_diumumkan_at' => now()->subHours(2),
            'catatan_pengumuman' => 'Hasil resmi PPDB diumumkan melalui portal madrasah. Peserta yang lulus wajib melakukan daftar ulang sesuai jadwal.',
            'is_active' => true,
        ]);

        $tracks = $createTracksAndQuotas($period);

        $futurePeriod = PpdbPeriod::create([
            'nama_periode' => 'PPDB Gelombang 1 2027/2028',
            'tahun_ajaran' => '2027/2028',
            'tahun_mulai' => 2027,
            'tahun_selesai' => 2028,
            'gelombang_ke' => 1,
            'gelombang_label' => 'Gelombang 1',
            'tanggal_mulai_pendaftaran' => now()->addMonths(9)->toDateString(),
            'tanggal_selesai_pendaftaran' => now()->addMonths(11)->toDateString(),
            'tanggal_pengumuman' => now()->addYear()->toDateString(),
            'tanggal_mulai_daftar_ulang' => now()->addYear()->addDay()->toDateString(),
            'tanggal_selesai_daftar_ulang' => now()->addYear()->addWeek()->toDateString(),
            'deskripsi' => 'Periode awal untuk persiapan PPDB tahun ajaran 2027/2028.',
            'status' => 'published',
            'status_pengumuman' => 'draft',
            'hasil_diumumkan_at' => null,
            'catatan_pengumuman' => null,
            'is_active' => false,
        ]);

        $createTracksAndQuotas($futurePeriod);

        $sampleApplicants = [
            [
                'nama_lengkap' => 'Muhammad Alif Pratama',
                'nisn' => '0098712345',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kolaka',
                'tanggal_lahir' => '2010-04-10',
                'alamat_lengkap' => 'Jl. Pemuda Baru No. 7, Kolaka',
                'nomor_hp' => '081340001111',
                'asal_sekolah' => 'SMP Negeri 1 Kolaka',
                'track' => 'reguler',
                'pilihan_1' => 'IPA',
                'pilihan_2' => 'IPS',
                'status_pendaftaran' => 'submitted',
                'status_berkas' => 'pending',
                'skor_tes_dasar' => 78,
                'skor_wawancara' => 80,
            ],
            [
                'nama_lengkap' => 'Sitti Aulia Rahma',
                'nisn' => '0098712355',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Kolaka',
                'tanggal_lahir' => '2010-07-18',
                'alamat_lengkap' => 'Jl. Pendidikan No. 15, Kolaka',
                'nomor_hp' => '081340002222',
                'asal_sekolah' => 'MTs Negeri Kolaka',
                'track' => 'prestasi',
                'pilihan_1' => 'KAG',
                'pilihan_2' => 'IPS',
                'status_pendaftaran' => 'needs_revision',
                'status_berkas' => 'revision',
                'skor_prestasi' => 88,
                'skor_tes_dasar' => 81,
                'skor_wawancara' => 84,
            ],
            [
                'nama_lengkap' => 'La Ode Fajar Hidayat',
                'nisn' => '0098712365',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kolaka',
                'tanggal_lahir' => '2010-01-22',
                'alamat_lengkap' => 'Jl. Veteran Lorong 3, Kolaka',
                'nomor_hp' => '081340003333',
                'asal_sekolah' => 'SMP Negeri 3 Kolaka',
                'track' => 'afirmasi',
                'pilihan_1' => 'IPS',
                'pilihan_2' => 'BBK',
                'status_pendaftaran' => 'verified',
                'status_berkas' => 'verified',
                'skor_afirmasi' => 91,
                'skor_tes_dasar' => 79,
                'skor_wawancara' => 83,
            ],
            [
                'nama_lengkap' => 'Nur Asmi Cahyani',
                'nisn' => '0098712375',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Pomalaa',
                'tanggal_lahir' => '2010-11-03',
                'alamat_lengkap' => 'Jl. Mekar Sari No. 4, Pomalaa',
                'nomor_hp' => '081340004444',
                'asal_sekolah' => 'SMP Negeri 2 Pomalaa',
                'track' => 'reguler',
                'pilihan_1' => 'IPA',
                'pilihan_2' => 'IPS',
                'status_pendaftaran' => 'under_review',
                'status_berkas' => 'complete',
                'skor_tes_dasar' => 86,
                'skor_wawancara' => 87,
            ],
            [
                'nama_lengkap' => 'Rahmat Saputra',
                'nisn' => '0098712385',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Kolaka Timur',
                'tanggal_lahir' => '2010-06-29',
                'alamat_lengkap' => 'Desa Lambandia, Kolaka Timur',
                'nomor_hp' => '081340005555',
                'asal_sekolah' => 'SMP Negeri 1 Lambandia',
                'track' => 'prestasi',
                'pilihan_1' => 'KAG',
                'pilihan_2' => 'IPA',
                'status_pendaftaran' => 'accepted',
                'status_berkas' => 'verified',
                'skor_prestasi' => 95,
                'skor_tes_dasar' => 90,
                'skor_wawancara' => 92,
            ],
            [
                'nama_lengkap' => 'Wa Ode Melati Safitri',
                'nisn' => '0098712395',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Kolaka',
                'tanggal_lahir' => '2010-02-14',
                'alamat_lengkap' => 'Jl. Mangga Raya No. 11, Kolaka',
                'nomor_hp' => '081340006666',
                'asal_sekolah' => 'SMP Negeri 4 Kolaka',
                'track' => 'afirmasi',
                'pilihan_1' => 'BBK',
                'pilihan_2' => 'IPS',
                'status_pendaftaran' => 'rejected',
                'status_berkas' => 'verified',
                'skor_afirmasi' => 72,
                'skor_tes_dasar' => 69,
                'skor_wawancara' => 74,
            ],
        ];

        foreach ($sampleApplicants as $index => $applicant) {
            $application = PpdbApplication::create([
                'period_id' => $period->id,
                'track_id' => $tracks[$applicant['track']]->id,
                'nomor_pendaftaran' => 'PPDB-' . now()->format('Y') . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'nama_lengkap' => $applicant['nama_lengkap'],
                'nisn' => $applicant['nisn'],
                'jenis_kelamin' => $applicant['jenis_kelamin'],
                'tempat_lahir' => $applicant['tempat_lahir'],
                'tanggal_lahir' => $applicant['tanggal_lahir'],
                'agama' => 'Islam',
                'alamat_lengkap' => $applicant['alamat_lengkap'],
                'nomor_hp' => $applicant['nomor_hp'],
                'email' => 'calon' . ($index + 1) . '@mail.com',
                'asal_sekolah' => $applicant['asal_sekolah'],
                'nama_ayah' => 'Ayah ' . explode(' ', $applicant['nama_lengkap'])[0],
                'pekerjaan_ayah' => 'Wiraswasta',
                'nama_ibu' => 'Ibu ' . explode(' ', $applicant['nama_lengkap'])[0],
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'nomor_hp_orang_tua' => '08134000999' . $index,
                'pilihan_program_1_id' => $programIds[$applicant['pilihan_1']],
                'pilihan_program_2_id' => $programIds[$applicant['pilihan_2']],
                'nilai_rata_rata' => 84 + ($index * 2),
                'skor_akademik' => 84 + ($index * 2),
                'skor_prestasi' => $applicant['skor_prestasi'] ?? null,
                'skor_afirmasi' => $applicant['skor_afirmasi'] ?? null,
                'skor_tes_dasar' => $applicant['skor_tes_dasar'] ?? null,
                'skor_wawancara' => $applicant['skor_wawancara'] ?? null,
                'skor_berkas' => in_array($applicant['status_berkas'], ['verified', 'complete'], true) ? 90 : 55,
                'catatan_pendaftar' => 'Pendaftar dummy untuk pengujian modul fase 1 PPDB.',
                'catatan_verifikator' => match ($applicant['status_pendaftaran']) {
                    'needs_revision' => 'Scan rapor kurang jelas, mohon upload ulang.',
                    'accepted' => 'Lolos tahap verifikasi administrasi dan direkomendasikan diterima.',
                    'rejected' => 'Kuota jalur penuh dan dokumen afirmasi tidak memenuhi syarat prioritas.',
                    'verified' => 'Seluruh berkas valid dan siap masuk tahap penetapan.',
                    default => null,
                },
                'status_pendaftaran' => $applicant['status_pendaftaran'],
                'status_berkas' => $applicant['status_berkas'],
                'submitted_at' => now()->subDays(2 - min($index, 2)),
                'verified_at' => in_array($applicant['status_pendaftaran'], ['verified', 'accepted', 'rejected'], true) ? now()->subDay() : null,
                'verified_by' => in_array($applicant['status_pendaftaran'], ['verified', 'accepted', 'rejected', 'needs_revision'], true) ? $ppdbAdmin->id : null,
            ]);

            foreach (['Kartu Keluarga', 'Akta Kelahiran', 'Rapor / Nilai', 'Pas Foto'] as $jenisDokumen) {
                PpdbDocument::create([
                    'application_id' => $application->id,
                    'jenis_dokumen' => $jenisDokumen,
                    'file_path' => 'ppdb/sample-' . Str::slug($jenisDokumen) . '-' . ($index + 1) . '.pdf',
                    'status_verifikasi' => match ($applicant['status_pendaftaran']) {
                        'needs_revision' => 'revision',
                        'verified', 'accepted', 'rejected' => 'approved',
                        default => 'pending',
                    },
                    'catatan_verifikasi' => $applicant['status_pendaftaran'] === 'needs_revision' ? 'Perlu unggah ulang dokumen ini.' : null,
                ]);
            }
        }

        app(PpdbSelectionService::class)->processPeriod($period);

        $acceptedForReRegistration = PpdbApplication::where('period_id', $period->id)
            ->where('hasil_seleksi', 'passed')
            ->orderBy('skor_seleksi', 'desc')
            ->first();

        if ($acceptedForReRegistration) {
            $acceptedForReRegistration->update([
                'status_daftar_ulang' => 'verified',
                'daftar_ulang_at' => now()->subHour(),
                'catatan_daftar_ulang' => 'Daftar ulang telah diverifikasi oleh panitia.',
                'verified_daftar_ulang_by' => $ppdbAdmin->id,
                'verified_daftar_ulang_at' => now()->subMinutes(30),
            ]);
        }

        // ── Pegawai ───────────────────────────────────
        $pegawaiData = [
            ['nama_lengkap' => 'Drs. H. Muhammad Arfan, M.Pd.', 'jabatan' => 'Kepala Madrasah', 'nip' => '196508151990031012', 'bidang_tugas' => 'Pimpinan'],
            ['nama_lengkap' => 'Hj. Sitti Rahmawati, S.Pd., M.Si.', 'jabatan' => 'Wakil Kepala Madrasah Bidang Kurikulum', 'nip' => '197204231998022003', 'bidang_tugas' => 'Kurikulum'],
            ['nama_lengkap' => 'Ir. Abdul Kadir, M.T.', 'jabatan' => 'Wakil Kepala Madrasah Bidang Sarana', 'nip' => '196912101995031005', 'bidang_tugas' => 'Sarana Prasarana'],
            ['nama_lengkap' => 'Drs. La Ode Hasanuddin', 'jabatan' => 'Wakil Kepala Madrasah Bidang Kesiswaan', 'nip' => '197001051997031008', 'bidang_tugas' => 'Kesiswaan'],
            ['nama_lengkap' => 'Wa Ode Nurhaliza, S.Si., M.Sc.', 'jabatan' => 'Koordinator Peminatan IPA', 'nip' => '198506102010032015', 'bidang_tugas' => 'Ilmu Pengetahuan Alam'],
            ['nama_lengkap' => 'Andi Firmansyah, S.E., M.M.', 'jabatan' => 'Koordinator Peminatan IPS', 'nip' => '198709222012031009', 'bidang_tugas' => 'Ilmu Pengetahuan Sosial'],
            ['nama_lengkap' => 'Hj. Nurhayati, S.Pd., M.Hum.', 'jabatan' => 'Koordinator Peminatan Bahasa dan Budaya', 'nip' => '197805142003022006', 'bidang_tugas' => 'Bahasa dan Budaya'],
            ['nama_lengkap' => 'Muh. Reza Pahlevi, S.Ag., M.Pd.I.', 'jabatan' => 'Koordinator Peminatan Keagamaan', 'nip' => '198203172007011004', 'bidang_tugas' => 'Keagamaan'],
            ['nama_lengkap' => 'Sitti Aminah, S.Pd.', 'jabatan' => 'Guru Bahasa Indonesia', 'nip' => '198005232005022003', 'bidang_tugas' => 'Normatif'],
            ['nama_lengkap' => 'Drs. Ahmad Yani', 'jabatan' => 'Guru Matematika', 'nip' => '196711201993031010', 'bidang_tugas' => 'Normatif'],
            ['nama_lengkap' => 'Fitriani Putri, S.Pd., M.Pd.', 'jabatan' => 'Guru Bahasa Inggris', 'nip' => '199102142016032008', 'bidang_tugas' => 'Normatif'],
            ['nama_lengkap' => 'La Ode Rahman, S.Ag.', 'jabatan' => 'Guru Pendidikan Agama Islam', 'nip' => '197503181999031011', 'bidang_tugas' => 'Normatif'],
            ['nama_lengkap' => 'Nur Hidayat, S.Pd.', 'jabatan' => 'Guru PJOK', 'nip' => '198607092010031012', 'bidang_tugas' => 'Normatif'],
            ['nama_lengkap' => 'Wa Ode Siti Mariam, S.Pd.', 'jabatan' => 'Guru BK', 'nip' => '198904152013032007', 'bidang_tugas' => 'Bimbingan Konseling'],
            ['nama_lengkap' => 'Hasan Basri, S.Sos.', 'jabatan' => 'Kepala Tata Usaha', 'nip' => '197209101996031005', 'bidang_tugas' => 'Tata Usaha'],
        ];

        foreach ($pegawaiData as $p) {
            Pegawai::create([
                'nip' => $p['nip'],
                'nama_lengkap' => $p['nama_lengkap'],
                'jabatan' => $p['jabatan'],
                'bidang_tugas' => $p['bidang_tugas'],
                'status_aktif' => true,
            ]);
        }

        // ── Kategori Berita ───────────────────────────
        $kategoriBerita = [
            'Kegiatan Madrasah', 'Prestasi', 'Pengumuman Resmi', 'PPDB',
            'Kerja Sama Mitra', 'Teknologi', 'Olahraga', 'Seni & Budaya',
        ];
        $katIds = [];
        foreach ($kategoriBerita as $kb) {
            $k = KategoriBerita::create([
                'nama_kategori' => $kb,
                'slug' => Str::slug($kb),
            ]);
            $katIds[] = $k->id;
        }

        // ── Berita ────────────────────────────────────
        $beritaData = [
            [
                'judul' => 'MAN 2 Kolaka Raih Juara Umum LKS Tingkat Provinsi Sulawesi Tenggara 2026',
                'kategori_idx' => 1,
                'konten' => '<p>MAN 2 Kolaka kembali mengukir prestasi gemilang dengan meraih juara umum dalam ajang Lomba Kompetensi Siswa (LKS) tingkat Provinsi Sulawesi Tenggara tahun 2026 yang diselenggarakan di Kota Kendari.</p><p>Dari total 12 bidang lomba yang diikuti, siswa-siswi MAN 2 Kolaka berhasil membawa pulang 5 medali emas, 4 medali perak, dan 3 medali perunggu. Prestasi ini menjadi yang terbaik dalam sejarah keikutsertaan madrasah di ajang LKS.</p><p>"Keberhasilan ini tidak lepas dari kerja keras siswa, dedikasi guru pembimbing, dan dukungan penuh dari semua stakeholder madrasah," ujar Kepala Madrasah Drs. H. Muhammad Arfan, M.Pd. saat konfrensi pers di aula madrasah.</p><p>Beberapa bidang lomba yang berhasil diraih medali emas antara lain Web Technologies, IT Network Systems Administration, Graphic Design Technology, Accounting, dan Office Administration.</p>',
                'days_ago' => 2,
            ],
            [
                'judul' => 'Penandatanganan MoU dengan PT Telkom Indonesia untuk Program Magang Siswa',
                'kategori_idx' => 4,
                'konten' => '<p>MAN 2 Kolaka resmi menandatangani nota kesepahaman (MoU) dengan PT Telkom Indonesia Tbk untuk program magang siswa dan pengembangan kurikulum berbasis industri. Penandatanganan dilakukan oleh Kepala Madrasah dan General Manager Telkom Regional VII Sulawesi.</p><p>Melalui kerja sama ini, siswa peminatan TKJ dan RPL akan mendapat kesempatan magang selama 6 bulan di kantor Telkom dan anak perusahaannya. Selain itu, Telkom juga akan memberikan pelatihan sertifikasi bagi guru-guru produktif.</p><p>"Ini adalah langkah strategis untuk memastikan lulusan kami memiliki kompetensi yang benar-benar dibutuhkan industri telekomunikasi dan IT," jelas Wa Ode Nurhaliza, S.Kom., M.Cs., Koordinator Peminatan RPL.</p>',
                'days_ago' => 5,
            ],
            [
                'judul' => 'Workshop Internet of Things (IoT) Bersama Dosen ITB untuk Siswa Peminatan TKJ',
                'kategori_idx' => 5,
                'konten' => '<p>Dalam rangka meningkatkan kompetensi siswa di bidang teknologi terkini, MAN 2 Kolaka menggelar workshop Internet of Things (IoT) yang menghadirkan narasumber langsung dari Institut Teknologi Bandung (ITB).</p><p>Workshop yang berlangsung selama tiga hari ini diikuti oleh 60 siswa peminatan TKJ kelas XI dan XII. Para siswa belajar merancang dan membangun prototipe sistem IoT menggunakan mikrokontroler ESP32, sensor-sensor lingkungan, dan platform cloud untuk monitoring data secara real-time.</p><p>Dr. Ir. Budi Santoso dari Departemen Teknik Elektro ITB menyatakan kekagumannya terhadap antusiasme dan kemampuan dasar siswa MAN 2 Kolaka. "Saya senang melihat siswa-siswa di sini sudah memiliki fondasi yang kuat. Dengan bimbingan yang tepat, mereka bisa menjadi inovator di bidang IoT," ujarnya.</p>',
                'days_ago' => 8,
            ],
            [
                'judul' => 'Tim Futsal MAN 2 Kolaka Juara 1 Turnamen Antar SMK Se-Sulawesi Tenggara',
                'kategori_idx' => 6,
                'konten' => '<p>Tim futsal MAN 2 Kolaka berhasil menggondol trofi juara pertama dalam Turnamen Futsal Antar SMK Se-Sulawesi Tenggara 2026 yang dihelat di GOR Bahteramas Kendari.</p><p>Dalam pertandingan final yang berlangsung sengit, tim MAN 2 Kolaka mengalahkan SMKN 2 Kendari dengan skor 4-2. Gol-gol kemenangan dicetak oleh Andi Pratama (2 gol), La Ode Faisal, dan Muh. Rizky.</p><p>Pelatih tim, Nur Hidayat, S.Pd., menyatakan bangga dengan perjuangan anak-anak didiknya. "Mereka berlatih sangat disiplin selama tiga bulan terakhir. Kemenangan ini adalah buah dari kerja keras mereka," katanya.</p>',
                'days_ago' => 12,
            ],
            [
                'judul' => 'Pengumuman Jadwal dan Syarat Pendaftaran PPDB 2026/2027 MAN 2 Kolaka',
                'kategori_idx' => 3,
                'konten' => '<p>MAN 2 Kolaka membuka pendaftaran peserta didik baru (PPDB) tahun ajaran 2026/2027. Pendaftaran dibuka mulai tanggal 1 April hingga 30 Juni 2026 melalui sistem online dan offline.</p><p><strong>Persyaratan Umum:</strong></p><ul><li>Ijazah atau Surat Keterangan Lulus SMP/MTs sederajat</li><li>Rapor semester 1-5 SMP/MTs</li><li>Akta kelahiran</li><li>Kartu Keluarga</li><li>Pas foto 3x4 sebanyak 4 lembar</li><li>Surat keterangan sehat dari dokter</li></ul><p><strong>Daya Tampung:</strong></p><ul><li>RPL: 72 siswa (2 rombel)</li><li>TKJ: 72 siswa (2 rombel)</li><li>OTKP: 36 siswa (1 rombel)</li><li>AKL: 36 siswa (1 rombel)</li><li>TBSM: 72 siswa (2 rombel)</li></ul>',
                'days_ago' => 1,
            ],
            [
                'judul' => 'Siswa Peminatan RPL Berhasil Develop Aplikasi E-Kantin yang Digunakan Seluruh Warga Madrasah',
                'kategori_idx' => 5,
                'konten' => '<p>Sebuah inovasi membanggakan lahir dari tangan siswa peminatan Rekayasa Perangkat Lunak (RPL) kelas XII. Tim yang terdiri dari Muh. Farhan, Sitti Aisyah, dan La Ode Akbar berhasil mengembangkan aplikasi E-Kantin yang kini digunakan oleh seluruh warga madrasah.</p><p>Aplikasi berbasis web dan mobile ini memungkinkan siswa dan guru untuk memesan makanan dari kantin madrasah secara digital, melakukan pembayaran cashless, dan memantau status pesanan secara real-time.</p><p>"Aplikasi ini lahir dari proyek berbasis praktik di semester 5. Kami sangat bangga karena ternyata hasilnya benar-benar bermanfaat dan digunakan sehari-hari," ungkap Muh. Farhan, ketua tim pengembang.</p>',
                'days_ago' => 15,
            ],
            [
                'judul' => 'Peringatan Hari Pendidikan Nasional: MAN 2 Kolaka Gelar Upacara dan Pentas Seni',
                'kategori_idx' => 7,
                'konten' => '<p>Dalam rangka memperingati Hari Pendidikan Nasional (Hardiknas) 2026, MAN 2 Kolaka menggelar rangkaian kegiatan yang meriah, dimulai dari upacara bendera, lomba antar kelas, hingga pentas seni budaya.</p><p>Upacara peringatan Hardiknas dipimpin langsung oleh Kepala Madrasah dengan pembacaan pidato Menteri Pendidikan. Seluruh civitas akademika hadir dengan khidmat mengenakan pakaian adat daerah masing-masing.</p><p>Acara dilanjutkan dengan pentas seni yang menampilkan berbagai kesenian daerah Sulawesi Tenggara seperti tari Lulo, musik Gambus, dan drama musikal bertema pendidikan. Festival ini menjadi ajang unjuk bakat sekaligus memperkuat kecintaan terhadap budaya lokal.</p>',
                'days_ago' => 20,
            ],
            [
                'judul' => 'Program Praktik AKL: Siswa Kelola Pembukuan UMKM Binaan Madrasah',
                'kategori_idx' => 0,
                'konten' => '<p>Program praktik peminatan Akuntansi dan Keuangan Lembaga (AKL) MAN 2 Kolaka memasuki babak baru dengan peluncuran program pendampingan pembukuan untuk 15 UMKM binaan madrasah di sekitar Kabupaten Kolaka.</p><p>Siswa kelas XI dan XII AKL akan secara langsung membantu para pelaku UMKM dalam menyusun laporan keuangan sederhana, menghitung pajak, dan menggunakan aplikasi akuntansi. Program ini berlangsung selama satu semester penuh.</p><p>"Ini adalah pengalaman belajar yang tidak bisa didapat dari buku. Siswa langsung berhadapan dengan kasus nyata dan belajar berkomunikasi dengan klien," jelas Muh. Reza Pahlevi, S.E., M.Ak., Koordinator Peminatan AKL.</p>',
                'days_ago' => 25,
            ],
            [
                'judul' => 'MAN 2 Kolaka Terima Kunjungan Benchmarking dari 5 SMK Se-Sulawesi',
                'kategori_idx' => 0,
                'konten' => '<p>MAN 2 Kolaka menerima kunjungan studi banding (benchmarking) dari lima SMK yang tersebar di Sulawesi, yaitu SMKN 2 Kendari, SMKN 1 Makassar, SMKN 3 Palu, SMKN 1 Gorontalo, dan SMKN 2 Manado.</p><p>Kunjungan ini bertujuan untuk berbagi praktik terbaik dalam pengelolaan madrasah, implementasi pembelajaran berbasis proyek, dan strategi peningkatan mutu lulusan. Delegasi dari kelima sekolah mendapat kesempatan berkeliling melihat fasilitas laboratorium, workshop, dan unit produksi.</p><p>"Kami sangat terinspirasi dengan apa yang sudah dicapai MAN 2 Kolaka, terutama dalam hal pembelajaran berbasis proyek dan kerja sama mitra," ungkap salah satu kepala sekolah peserta benchmarking.</p>',
                'days_ago' => 30,
            ],
            [
                'judul' => 'Pelatihan Cybersecurity untuk Guru TKJ dari BSSN (Badan Siber dan Sandi Negara)',
                'kategori_idx' => 5,
                'konten' => '<p>Guru-guru produktif peminatan Teknik Komputer dan Jaringan (TKJ) MAN 2 Kolaka mengikuti pelatihan intensif Cybersecurity yang diselenggarakan bekerja sama dengan Badan Siber dan Sandi Negara (BSSN).</p><p>Pelatihan selama lima hari ini mencakup materi ethical hacking, network security, digital forensics, dan incident response. Para guru mendapat sertifikat kompetensi yang akan meningkatkan kualitas pengajaran di kelas.</p><p>"Keamanan siber adalah topik yang sangat penting di era digital ini. Dengan pelatihan ini, guru-guru kami bisa menyampaikan materi yang up-to-date kepada siswa," kata Andi Firmansyah, S.Kom., Koordinator Peminatan TKJ.</p>',
                'days_ago' => 35,
            ],
        ];

        foreach ($beritaData as $b) {
            Berita::create([
                'user_id' => $admin->id,
                'kategori_id' => $katIds[$b['kategori_idx']],
                'judul' => $b['judul'],
                'slug' => Str::slug($b['judul']),
                'konten_html' => $b['konten'],
                'status_publikasi' => 'published',
                'view_count' => rand(50, 500),
                'published_at' => now()->subDays($b['days_ago']),
            ]);
        }

        // ── Pengumuman ────────────────────────────────
        $pengumumanData = [
            ['judul' => 'Pendaftaran Peserta Didik Baru (PPDB) 2026/2027 Telah Dibuka!', 'isi' => 'Pendaftaran PPDB MAN 2 Kolaka untuk tahun ajaran 2026/2027 resmi dibuka mulai 1 April 2026. Segera daftarkan diri Anda melalui website resmi atau datang langsung ke madrasah. Info lebih lanjut hubungi panitia PPDB.', 'mulai' => 0, 'akhir' => 90],
            ['judul' => 'Ujian Akhir Semester Genap Dimulai 20 Maret 2026', 'isi' => 'Diberitahukan kepada seluruh siswa bahwa Ujian Akhir Semester (UAS) Genap akan dilaksanakan mulai tanggal 20 Maret hingga 31 Maret 2026. Harap mempersiapkan diri dengan baik. Jadwal lengkap dapat dilihat di papan pengumuman dan website madrasah.', 'mulai' => -5, 'akhir' => 20],
            ['judul' => 'Pengumpulan Berkas Magang Industri Kelas XI Paling Lambat 25 Maret 2026', 'isi' => 'Seluruh siswa kelas XI yang akan mengikuti program Praktik Kerja Lapangan (PKL)/Magang Industri diwajibkan mengumpulkan berkas persyaratan paling lambat tanggal 25 Maret 2026 ke masing-masing wali kelas.', 'mulai' => -10, 'akhir' => 15],
            ['judul' => 'Libur Hari Raya Nyepi dan Ramadhan 1447 H', 'isi' => 'Sehubungan dengan Hari Raya Nyepi dan memasuki bulan suci Ramadhan, kegiatan belajar mengajar diliburkan mulai 28 Maret - 30 Maret 2026. KBM kembali normal pada Senin, 31 Maret 2026.', 'mulai' => -3, 'akhir' => 18],
            ['judul' => 'Pendaftaran Sertifikasi Kompetensi BNSP Batch Maret 2026', 'isi' => 'Pendaftaran uji sertifikasi kompetensi BNSP untuk siswa kelas XII semua peminatan telah dibuka. Biaya sertifikasi GRATIS ditanggung madrasah. Segera daftarkan diri ke koordinator peminatan masing-masing.', 'mulai' => -7, 'akhir' => 25],
        ];

        foreach ($pengumumanData as $p) {
            Pengumuman::create([
                'user_id' => $admin->id,
                'judul_pengumuman' => $p['judul'],
                'slug' => Str::slug($p['judul']),
                'isi_pengumuman' => $p['isi'],
                'tanggal_mulai_tampil' => now()->addDays($p['mulai']),
                'tanggal_akhir_tampil' => now()->addDays($p['akhir']),
            ]);
        }

        // ── Agenda ────────────────────────────────────
        $agendaData = [
            ['nama' => 'Upacara Peringatan Hari Pendidikan Nasional', 'deskripsi' => 'Upacara bendera dalam rangka memperingati Hari Pendidikan Nasional 2 Mei 2026. Seluruh warga madrasah wajib hadir.', 'lokasi' => 'Lapangan Utama MAN 2 Kolaka', 'mulai' => 48, 'durasi' => 3, 'kat' => 'umum'],
            ['nama' => 'Workshop Pembuatan Website dengan Laravel', 'deskripsi' => 'Workshop intensif bagi siswa RPL tentang pengembangan web modern menggunakan framework Laravel dan Livewire.', 'lokasi' => 'Lab RPL Gedung B Lt. 2', 'mulai' => 10, 'durasi' => 8, 'kat' => 'siswa'],
            ['nama' => 'Rapat Koordinasi Persiapan PPDB 2026/2027', 'deskripsi' => 'Rapat koordinasi seluruh panitia PPDB untuk membahas mekanisme, jadwal, dan pembagian tugas.', 'lokasi' => 'Ruang Rapat Utama', 'mulai' => 5, 'durasi' => 3, 'kat' => 'staf'],
            ['nama' => 'Lomba Karya Ilmiah Remaja (KIR) Internal', 'deskripsi' => 'Lomba karya ilmiah remaja tingkat madrasah sebagai seleksi untuk mewakili madrasah di tingkat kabupaten.', 'lokasi' => 'Aula Serbaguna', 'mulai' => 15, 'durasi' => 6, 'kat' => 'siswa'],
            ['nama' => 'Pelatihan K3 untuk Siswa Peminatan TBSM', 'deskripsi' => 'Pelatihan Keselamatan dan Kesehatan Kerja (K3) wajib bagi siswa peminatan Teknik dan Bisnis Sepeda Motor sebelum praktik di bengkel.', 'lokasi' => 'Workshop TBSM', 'mulai' => 7, 'durasi' => 4, 'kat' => 'siswa'],
            ['nama' => 'Kunjungan Industri ke PT Semen Tonasa', 'deskripsi' => 'Kunjungan industri untuk siswa kelas XI semua peminatan ke PT Semen Tonasa di Pangkep, Sulawesi Selatan.', 'lokasi' => 'PT Semen Tonasa, Pangkep', 'mulai' => 22, 'durasi' => 24, 'kat' => 'siswa'],
            ['nama' => 'Seminar Motivasi: Membangun Mental Juara', 'deskripsi' => 'Seminar motivasi untuk seluruh siswa menghadirkan motivator nasional tentang pentingnya mental juara di dunia kerja.', 'lokasi' => 'Aula Serbaguna', 'mulai' => 18, 'durasi' => 3, 'kat' => 'umum'],
            ['nama' => 'Ujian Sertifikasi BNSP Batch Maret 2026', 'deskripsi' => 'Pelaksanaan uji sertifikasi kompetensi BNSP untuk siswa kelas XII seluruh peminatan.', 'lokasi' => 'Ruang LSP-P1 MAN 2 Kolaka', 'mulai' => 12, 'durasi' => 8, 'kat' => 'siswa'],
        ];

        foreach ($agendaData as $a) {
            Agenda::create([
                'user_id' => $admin->id,
                'nama_kegiatan' => $a['nama'],
                'slug' => Str::slug($a['nama']),
                'deskripsi_kegiatan' => $a['deskripsi'],
                'lokasi_pelaksanaan' => $a['lokasi'],
                'waktu_mulai' => now()->addDays($a['mulai'])->setTime(8, 0),
                'waktu_selesai' => now()->addDays($a['mulai'])->setTime(8 + $a['durasi'], 0),
                'kategori_peserta' => $a['kat'],
            ]);
        }

        // ── Ekstrakurikuler Kategori & Data ──────────
        $ekskulKats = [
            ['nama_kategori' => 'Olahraga',          'slug' => 'olahraga'],
            ['nama_kategori' => 'Seni & Budaya',     'slug' => 'seni-budaya'],
            ['nama_kategori' => 'Keagamaan',         'slug' => 'keagamaan'],
            ['nama_kategori' => 'Akademik & Sains',  'slug' => 'akademik-sains'],
            ['nama_kategori' => 'Kepemimpinan',      'slug' => 'kepemimpinan'],
        ];
        $ekskulKatIds = [];
        foreach ($ekskulKats as $ek) {
            $cat = EkstrakurikulerKategori::create($ek);
            $ekskulKatIds[$ek['slug']] = $cat->id;
        }

        $ekskulData = [
            ['program' => 'IPA',  'kategori' => 'akademik-sains',  'nama' => 'Karya Ilmiah Remaja (KIR)',          'deskripsi' => 'Wadah siswa mengembangkan kemampuan penelitian ilmiah dan mengikuti lomba karya tulis ilmiah tingkat kabupaten hingga nasional.',                                    'status' => 'tersedia'],
            ['program' => 'IPA',  'kategori' => 'akademik-sains',  'nama' => 'Olimpiade Sains Madrasah (OSM)',     'deskripsi' => 'Pembinaan intensif untuk persiapan olimpiade sains bidang Matematika, Fisika, Kimia, dan Biologi tingkat kabupaten, provinsi, dan nasional.',                    'status' => 'tersedia'],
            ['program' => 'IPS',  'kategori' => 'akademik-sains',  'nama' => 'Debat Bahasa Indonesia & Inggris',   'deskripsi' => 'Latihan debat kompetitif dalam Bahasa Indonesia dan Bahasa Inggris untuk mengasah kemampuan berpikir kritis dan komunikasi publik.',                              'status' => 'tersedia'],
            ['program' => 'BBK',  'kategori' => 'seni-budaya',     'nama' => 'Paduan Suara Madrasah',              'deskripsi' => 'Kelompok paduan suara yang aktif tampil pada upacara resmi, peringatan hari besar, dan kompetisi paduan suara tingkat daerah.',                                  'status' => 'tersedia'],
            ['program' => 'BBK',  'kategori' => 'seni-budaya',     'nama' => 'Teater & Seni Pertunjukan',          'deskripsi' => 'Ekskul seni peran dan pertunjukan yang melatih kreativitas, kepercayaan diri, dan ekspresi seni siswa melalui pementasan drama dan musikal.',                   'status' => 'tersedia'],
            ['program' => 'KAG',  'kategori' => 'keagamaan',       'nama' => 'Tahfidz Al-Qur\'an',                 'deskripsi' => 'Program hafalan Al-Qur\'an terstruktur dengan target minimal 2 juz per tahun, dibimbing oleh hafidz/hafidzah berpengalaman.',                                  'status' => 'tersedia'],
            ['program' => 'KAG',  'kategori' => 'keagamaan',       'nama' => 'Rohis (Rohani Islam)',               'deskripsi' => 'Organisasi keislaman yang mengelola kajian rutin, peringatan hari besar Islam, dan kegiatan sosial berbasis nilai-nilai Islam.',                                  'status' => 'tersedia'],
            ['program' => 'IPA',  'kategori' => 'olahraga',        'nama' => 'Futsal',                             'deskripsi' => 'Tim futsal madrasah yang aktif berlatih dan mengikuti turnamen antar madrasah/sekolah tingkat kabupaten dan provinsi.',                                          'status' => 'tersedia'],
            ['program' => 'IPS',  'kategori' => 'olahraga',        'nama' => 'Bola Voli',                          'deskripsi' => 'Ekskul bola voli putra dan putri dengan jadwal latihan rutin dan keikutsertaan dalam kompetisi olahraga pelajar daerah.',                                       'status' => 'tersedia'],
            ['program' => 'IPA',  'kategori' => 'kepemimpinan',    'nama' => 'Pramuka',                            'deskripsi' => 'Gerakan Pramuka sebagai ekskul wajib yang membentuk karakter, kedisiplinan, dan jiwa kepemimpinan siswa melalui kegiatan kepanduan.',                           'status' => 'tersedia'],
            ['program' => 'IPS',  'kategori' => 'kepemimpinan',    'nama' => 'OSIS & MPK',                         'deskripsi' => 'Organisasi Siswa Intra Sekolah sebagai wadah pengembangan kepemimpinan, organisasi, dan kegiatan kesiswaan di lingkungan madrasah.',                            'status' => 'tersedia'],
            ['program' => 'BBK',  'kategori' => 'seni-budaya',     'nama' => 'Jurnalistik & Majalah Dinding',      'deskripsi' => 'Ekskul jurnalistik yang melatih siswa menulis berita, membuat konten media sosial madrasah, dan mengelola majalah dinding.',                                   'status' => 'tersedia'],
        ];

        foreach ($ekskulData as $ek) {
            EkstrakurikulerKategori::find($ekskulKatIds[$ek['kategori']]);
            Ekstrakurikuler::create([
                'program_keahlian_id' => $programIds[$ek['program']],
                'kategori_id'         => $ekskulKatIds[$ek['kategori']],
                'nama_produk_jasa'    => $ek['nama'],
                'slug'                => Str::slug($ek['nama']),
                'deskripsi'           => $ek['deskripsi'],
                'harga_estimasi'      => null,
                'status_ketersediaan' => $ek['status'],
            ]);
        }

        // ── Galeri Album & Item ───────────────────────
        $albumData = [
            [
                'judul' => 'Upacara Hardiknas 2026',
                'deskripsi' => 'Dokumentasi kegiatan upacara peringatan Hari Pendidikan Nasional 2 Mei 2026.',
                'tanggal' => '2026-01-15',
                'items' => [
                    'Upacara bendera dipimpin Kepala Madrasah',
                    'Peserta upacara berpakaian adat',
                    'Pembacaan teks Sumpah Pemuda',
                    'Penampilan paduan suara',
                    'Foto bersama seluruh panitia',
                ],
            ],
            [
                'judul' => 'LKS Tingkat Provinsi Sultra 2026',
                'deskripsi' => 'Momen-momen keikutsertaan dan kemenangan siswa MAN 2 Kolaka dalam LKS Provinsi.',
                'tanggal' => '2026-02-10',
                'items' => [
                    'Tim RPL saat lomba Web Technologies',
                    'Suasana lomba IT Network Systems',
                    'Penyerahan medali emas',
                    'Selebrasi juara umum',
                    'Foto bersama dengan trofi',
                    'Pelepasan kontingen oleh Kepala Madrasah',
                ],
            ],
            [
                'judul' => 'Workshop IoT Bersama ITB',
                'deskripsi' => 'Kegiatan workshop Internet of Things (IoT) yang menghadirkan dosen ITB.',
                'tanggal' => '2026-03-01',
                'items' => [
                    'Pembukaan workshop oleh Wakil Kepala Madrasah',
                    'Praktek merakit sensor IoT',
                    'Sesi diskusi kelompok',
                    'Demo proyek siswa',
                ],
            ],
            [
                'judul' => 'Kunjungan Industri ke Makassar',
                'deskripsi' => 'Kunjungan industri siswa kelas XI ke berbagai perusahaan di Makassar.',
                'tanggal' => '2026-02-20',
                'items' => [
                    'Kunjungan ke kantor Telkom Makassar',
                    'Tour PT Kalla Group',
                    'Foto bersama di Pantai Losari',
                    'Sesi tanya jawab dengan HRD perusahaan',
                    'Makan bersama tim',
                ],
            ],
            [
                'judul' => 'Penandatanganan MoU dengan Telkom',
                'deskripsi' => 'Momen bersejarah penandatanganan kerja sama antara MAN 2 Kolaka dan PT Telkom Indonesia.',
                'tanggal' => '2026-03-10',
                'items' => [
                    'Prosesi penandatanganan MoU',
                    'Sambutan Kepala Madrasah',
                    'Sambutan GM Telkom Regional VII',
                    'Tukar cinderamata',
                    'Foto bersama jajaran pejabat',
                    'Tur fasilitas madrasah',
                ],
            ],
            [
                'judul' => 'Turnamen Futsal Antar SMK',
                'deskripsi' => 'Aksi tim futsal MAN 2 Kolaka di turnamen antar SMK se-Sulawesi Tenggara.',
                'tanggal' => '2026-03-05',
                'items' => [
                    'Pertandingan babak penyisihan',
                    'Gol spektakuler di semifinal',
                    'Selebrasi di partai final',
                    'Angkat trofi juara 1',
                    'Sesi foto bersama tim',
                ],
            ],
        ];

        foreach ($albumData as $alb) {
            $album = GaleriAlbum::create([
                'user_id' => $admin->id,
                'judul_album' => $alb['judul'],
                'slug' => Str::slug($alb['judul']),
                'deskripsi_singkat' => $alb['deskripsi'],
                'tanggal_kegiatan' => $alb['tanggal'],
            ]);

            foreach ($alb['items'] as $idx => $caption) {
                GaleriItem::create([
                    'album_id' => $album->id,
                    'tipe_file' => 'foto',
                    'file_path' => 'galeri/placeholder-' . $album->id . '-' . ($idx + 1) . '.jpg',
                    'caption' => $caption,
                ]);
            }
        }
    }
}
