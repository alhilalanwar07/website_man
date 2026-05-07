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
            <div class="line1">PEMERINTAH PROVINSI SULAWESI TENGGARA</div>
            <div class="line2">DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
            <div class="line3">SMK NEGERI 1 KOLAKA</div>
            <div class="line4">Jl. Pendidikan No. 49, Telp./Fax. (0405) 231378, Kab. Kolaka, 93517</div>
            <div class="line5">Email: smk1kolaka@gmail.com</div>
        </td>
        <td class="logo-cell">
            @if($rightLogoBase64 ?? null)
                <img src="{{ $rightLogoBase64 }}" alt="Logo SMK">
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
