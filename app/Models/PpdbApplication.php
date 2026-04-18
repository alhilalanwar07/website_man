<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PpdbApplication extends Model
{
    protected $table = 'ppdb_applications';

    protected $fillable = [
        'period_id',
        'track_id',
        'nomor_pendaftaran',
        'nama_lengkap',
        'nisn',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat_lengkap',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'tinggi_badan',
        'berat_badan',
        'gol_darah',
        'ukuran_seragam',
        'nomor_hp',
        'email',
        'asal_sekolah',
        'alamat_sekolah',
        'anak_ke',
        'jumlah_saudara',
        'nama_ayah',
        'tempat_tanggal_lahir_ayah',
        'pendidikan_terakhir_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'alamat_ayah',
        'kelurahan_ayah',
        'kecamatan_ayah',
        'nomor_hp_ayah',
        'nama_ibu',
        'tempat_tanggal_lahir_ibu',
        'pendidikan_terakhir_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'alamat_ibu',
        'kelurahan_ibu',
        'kecamatan_ibu',
        'nomor_hp_ibu',
        'nomor_hp_orang_tua',
        'pilihan_program_1_id',
        'pilihan_program_2_id',
        'nilai_rata_rata',
        'skor_akademik',
        'skor_prestasi',
        'skor_afirmasi',
        'skor_tes_dasar',
        'skor_wawancara',
        'skor_berkas',
        'skor_seleksi',
        'ranking_jalur',
        'ranking_program',
        'catatan_pendaftar',
        'persetujuan_data_at',
        'catatan_verifikator',
        'status_pendaftaran',
        'status_berkas',
        'hasil_seleksi',
        'program_diterima_id',
        'selection_notes',
        'status_daftar_ulang',
        'daftar_ulang_at',
        'catatan_daftar_ulang',
        'verified_daftar_ulang_by',
        'verified_daftar_ulang_at',
        'scored_at',
        'submitted_at',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'persetujuan_data_at' => 'datetime',
            'daftar_ulang_at' => 'datetime',
            'verified_daftar_ulang_at' => 'datetime',
            'scored_at' => 'datetime',
            'nilai_rata_rata' => 'decimal:2',
            'skor_akademik' => 'decimal:2',
            'skor_prestasi' => 'decimal:2',
            'skor_afirmasi' => 'decimal:2',
            'skor_tes_dasar' => 'decimal:2',
            'skor_wawancara' => 'decimal:2',
            'skor_berkas' => 'decimal:2',
            'skor_seleksi' => 'decimal:2',
        ];
    }

    public function getStatusPendaftaranLabelAttribute(): string
    {
        return $this->mapStatusLabel($this->status_pendaftaran, [
            'draft' => 'Draft',
            'submitted' => 'Menunggu Verifikasi',
            'under_review' => 'Sedang Ditinjau',
            'needs_revision' => 'Perlu Revisi',
            'verified' => 'Terverifikasi',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
        ]);
    }

    public function getStatusBerkasLabelAttribute(): string
    {
        return $this->mapStatusLabel($this->status_berkas, [
            'pending' => 'Menunggu Verifikasi',
            'incomplete' => 'Belum Lengkap',
            'complete' => 'Lengkap',
            'revision' => 'Perlu Revisi',
            'verified' => 'Sah',
            'rejected' => 'Ditolak',
        ]);
    }

    public function getHasilSeleksiLabelAttribute(): string
    {
        return $this->mapStatusLabel($this->hasil_seleksi, [
            'pending' => 'Menunggu Hasil',
            'passed' => 'Lulus',
            'reserve' => 'Cadangan',
            'failed' => 'Tidak Lulus',
        ]);
    }

    public function getStatusDaftarUlangLabelAttribute(): string
    {
        return $this->mapStatusLabel($this->status_daftar_ulang, [
            'not_available' => 'Belum Dibuka',
            'pending' => 'Belum Konfirmasi',
            'submitted' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
        ]);
    }

    protected function mapStatusLabel(?string $value, array $map): string
    {
        if (!$value) {
            return '-';
        }

        return $map[$value] ?? (string) Str::of($value)->replace('_', ' ')->title();
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PpdbPeriod::class, 'period_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(PpdbTrack::class, 'track_id');
    }

    public function pilihanProgram1(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class, 'pilihan_program_1_id');
    }

    public function pilihanProgram2(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class, 'pilihan_program_2_id');
    }

    public function programDiterima(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class, 'program_diterima_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function reRegistrationVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_daftar_ulang_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PpdbDocument::class, 'application_id');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(PpdbAchievement::class, 'application_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
