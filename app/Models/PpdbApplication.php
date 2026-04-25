<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PpdbApplication extends Model
{
    protected const APPROVED_BERKAS_STATUSES = ['verified', 'complete'];
    protected const APPROVED_REGISTRATION_STATUSES = ['verified', 'accepted'];
    protected const REJECTED_BERKAS_STATUSES = ['rejected'];
    protected const REJECTED_REGISTRATION_STATUSES = ['rejected'];

    protected $table = 'ppdb_applications';

    protected $fillable = [
        'period_id',
        'track_id',
        'nomor_pendaftaran',
        'nipd',
        'nama_lengkap',
        'nisn',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_kk',
        'no_registrasi_akta_lahir',
        'agama',
        'kewarganegaraan',
        'negara_asal',
        'kebutuhan_khusus',
        'alamat_lengkap',
        'rt_rw',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'nama_dusun',
        'kode_pos',
        'lintang',
        'bujur',
        'tempat_tinggal',
        'moda_transportasi',
        'tinggi_badan',
        'berat_badan',
        'lingkar_kepala',
        'gol_darah',
        'ukuran_seragam',
        'jarak_tempat_tinggal_kategori',
        'jarak_tempat_tinggal_km',
        'waktu_tempuh_jam',
        'waktu_tempuh_menit',
        'jenis_kesejahteraan',
        'nomor_kartu_kesejahteraan',
        'nama_di_kartu_kesejahteraan',
        'pekerjaan_warga_belajar',
        'punya_kip',
        'menerima_kip',
        'alasan_menolak_pip',
        'nomor_telepon_rumah',
        'nomor_hp',
        'email',
        'asal_sekolah',
        'alamat_sekolah',
        'anak_ke',
        'jumlah_saudara',
        'nama_ayah',
        'nik_ayah',
        'tempat_tanggal_lahir_ayah',
        'pendidikan_terakhir_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'kebutuhan_khusus_ayah',
        'alamat_ayah',
        'kelurahan_ayah',
        'kecamatan_ayah',
        'nomor_hp_ayah',
        'nama_ibu',
        'nik_ibu',
        'tempat_tanggal_lahir_ibu',
        'pendidikan_terakhir_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'kebutuhan_khusus_ibu',
        'alamat_ibu',
        'kelurahan_ibu',
        'kecamatan_ibu',
        'nomor_hp_ibu',
        'nomor_hp_orang_tua',
        'pilihan_program_1_id',
        'pilihan_program_2_id',
        'pilihan_program_3_id',
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
            'nipd' => 'integer',
            'punya_kip' => 'boolean',
            'menerima_kip' => 'boolean',
            'jarak_tempat_tinggal_km' => 'decimal:2',
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

    public function getVerificationStatusKeyAttribute(): string
    {
        if (in_array($this->status_berkas, self::REJECTED_BERKAS_STATUSES, true) || in_array($this->status_pendaftaran, self::REJECTED_REGISTRATION_STATUSES, true)) {
            return 'rejected';
        }

        if (
            in_array($this->status_berkas, self::APPROVED_BERKAS_STATUSES, true)
            || in_array($this->status_pendaftaran, self::APPROVED_REGISTRATION_STATUSES, true)
        ) {
            return 'approved';
        }

        return 'process';
    }

    public function getVerificationStatusLabelAttribute(): string
    {
        return $this->mapStatusLabel($this->verification_status_key, [
            'process' => 'Dalam Proses',
            'approved' => 'Terverifikasi / Diterima',
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

    public function scopeWhereVerificationStatus(Builder $query, string $status): Builder
    {
        if ($status === 'rejected') {
            return $query->where(function (Builder $statusQuery): void {
                $statusQuery->whereIn('status_berkas', self::REJECTED_BERKAS_STATUSES)
                    ->orWhereIn('status_pendaftaran', self::REJECTED_REGISTRATION_STATUSES);
            });
        }

        if ($status === 'approved') {
            return $query
                ->whereNotIn('status_berkas', self::REJECTED_BERKAS_STATUSES)
                ->whereNotIn('status_pendaftaran', self::REJECTED_REGISTRATION_STATUSES)
                ->where(function (Builder $statusQuery): void {
                    $statusQuery->whereIn('status_berkas', self::APPROVED_BERKAS_STATUSES)
                        ->orWhereIn('status_pendaftaran', self::APPROVED_REGISTRATION_STATUSES);
                });
        }

        if ($status === 'process') {
            return $query
                ->whereNotIn('status_berkas', self::REJECTED_BERKAS_STATUSES)
                ->whereNotIn('status_pendaftaran', self::REJECTED_REGISTRATION_STATUSES)
                ->where(function (Builder $statusQuery): void {
                    $statusQuery
                        ->whereNotIn('status_berkas', self::APPROVED_BERKAS_STATUSES)
                        ->whereNotIn('status_pendaftaran', self::APPROVED_REGISTRATION_STATUSES);
                });
        }

        return $query;
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

    public function pilihanProgram3(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class, 'pilihan_program_3_id');
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
