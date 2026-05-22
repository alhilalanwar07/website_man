@php
    $profil = $profil ?? \App\Models\ProfilSekolah::first();
    $title = $title ?? 'Daftar Peserta Didik';
@endphp
<table class="kop-table">
    <tr>
        <td class="logo-cell">
            @if($leftLogoBase64 ?? null)
                <img src="{{ $leftLogoBase64 }}" alt="Logo Sultra">
            @endif
        </td>
        <td class="kop-text">
            <div class="line1">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
            <div class="line2">KANTOR KEMENTERIAN AGAMA KABUPATEN KOLAKA</div>
            <div class="line3">{{ strtoupper($profil->nama_sekolah ?? 'MAN 2 KOLAKA') }}</div>
            <div class="line4">{{ $profil->alamat_lengkap ?? 'Jl. Pemuda No. 12, Kelurahan Laloeha, Kecamatan Kolaka, Kabupaten Kolaka, Sulawesi Tenggara 93511' }}{{ $profil->nomor_telepon ? ' | Telp. ' . $profil->nomor_telepon : '' }}</div>
            <div class="line5">Email: {{ $profil->email_resmi ?? 'info@man2kolaka.sch.id' }}</div>
        </td>
        <td class="logo-cell">
            @if($rightLogoBase64 ?? null)
                <img src="{{ $rightLogoBase64 }}" alt="Logo MAN">
            @endif
        </td>
    </tr>
</table>
<div class="kop-line"></div>
<div class="doc-title">
    <h1>{{ $title }}</h1>
    @if(isset($meta) && ($meta['academic_year'] ?? null))
        <p>Tahun Pelajaran {{ $meta['academic_year'] }} — Dicetak {{ now()->translatedFormat('d F Y') }}</p>
    @else
        <p>Dicetak {{ now()->translatedFormat('d F Y') }}</p>
    @endif
</div>
