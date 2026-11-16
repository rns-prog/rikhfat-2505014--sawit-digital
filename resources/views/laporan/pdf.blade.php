<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 30px; }
        body { color: #1B2333; font-family: "DejaVu Sans", sans-serif; font-size: 9px; }
        h1 { margin: 0 0 6px; color: #06246B; font-size: 18px; }
        h2 { margin: 18px 0 7px; color: #06246B; font-size: 12px; }
        p { margin: 3px 0; }
        .muted { color: #6B7690; }
        .meta { margin-bottom: 14px; line-height: 1.6; }
        .cards { width: 100%; margin-bottom: 14px; border-spacing: 6px 0; }
        .cards td { width: 25%; border: 1px solid #E6EAF2; padding: 9px; }
        .label { color: #6B7690; font-size: 8px; }
        .value { margin-top: 5px; font-size: 13px; font-weight: bold; }
        .positive { color: #1FA45B; }
        .negative { color: #E5484D; }
        table.data { width: 100%; border-collapse: collapse; }
        .data th, .data td { border: 1px solid #E6EAF2; padding: 6px 7px; }
        .data th { background: #E6EEFB; color: #1B2333; text-align: left; }
        .data .right { text-align: right; }
        tfoot { font-weight: bold; }
        .footer { margin-top: 16px; color: #6B7690; font-size: 8px; text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Untung-Rugi Kebun Sawit</h1>
    <div class="meta">
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Periode:</strong> Tahun {{ $report['year'] }} ({{ $report['mode'] === 'bulan' ? 'Rekap per bulan' : 'Rekap per tahun' }})</p>
        <p><strong>Kebun:</strong> {{ $report['selectedKebun']?->nama ?? 'Semua kebun' }}</p>
        <p><strong>Tanggal cetak:</strong> {{ $printedAt->format('d/m/Y H:i') }}</p>
    </div>

    <table class="cards">
        <tr>
            <td><div class="label">Total panen</div><div class="value">{{ number_format($report['summary']['panen_kg'], 0, ',', '.') }} kg</div></td>
            <td><div class="label">Total pendapatan</div><div class="value">Rp {{ number_format($report['summary']['pendapatan'], 0, ',', '.') }}</div></td>
            <td><div class="label">Total biaya</div><div class="value">Rp {{ number_format($report['summary']['biaya'], 0, ',', '.') }}</div></td>
            <td><div class="label">Laba bersih</div><div class="value {{ $report['summary']['laba'] >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($report['summary']['laba'], 0, ',', '.') }}</div></td>
        </tr>
    </table>

    <h2>Rekap {{ $report['mode'] === 'bulan' ? 'per bulan' : 'per tahun' }}</h2>
    <table class="data">
        <thead><tr><th>Periode</th><th class="right">Panen (kg)</th><th class="right">Pendapatan</th><th class="right">Biaya</th><th class="right">Laba bersih</th></tr></thead>
        <tbody>
            @foreach ($report['periodRows'] as $row)
                <tr>
                    <td>{{ $row['periode'] }}</td>
                    <td class="right">{{ number_format($row['panen_kg'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row['pendapatan'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row['biaya'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row['laba'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot><tr><td>Total {{ $report['year'] }}</td><td class="right">{{ number_format($report['summary']['panen_kg'], 0, ',', '.') }}</td><td class="right">Rp {{ number_format($report['summary']['pendapatan'], 0, ',', '.') }}</td><td class="right">Rp {{ number_format($report['summary']['biaya'], 0, ',', '.') }}</td><td class="right">Rp {{ number_format($report['summary']['laba'], 0, ',', '.') }}</td></tr></tfoot>
    </table>

    <h2>Perbandingan kebun / blok</h2>
    <table class="data">
        <thead><tr><th>Kebun</th><th class="right">Total kg</th><th class="right">Pendapatan</th><th class="right">Biaya</th><th class="right">Laba</th><th class="right">Produktivitas</th></tr></thead>
        <tbody>
            @forelse ($report['estateRows'] as $estate)
                <tr>
                    <td>{{ $estate['nama'] }}{{ $report['bestEstateName'] === $estate['nama'] ? ' (Paling untung)' : '' }}</td>
                    <td class="right">{{ number_format($estate['panen_kg'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($estate['pendapatan'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($estate['biaya'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($estate['laba'], 0, ',', '.') }}</td>
                    <td class="right">{{ $estate['produktivitas'] === null ? '—' : number_format($estate['produktivitas'], 2, ',', '.').' kg/ha' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada kebun untuk ditampilkan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Rincian biaya per kategori</h2>
    <table class="data">
        <thead><tr><th>Kategori</th><th class="right">Total</th><th class="right">Persentase</th></tr></thead>
        <tbody>
            @foreach ($report['categoryRows'] as $row)
                <tr><td>{{ $row['kategori'] }}</td><td class="right">Rp {{ number_format($row['total'], 0, ',', '.') }}</td><td class="right">{{ number_format($row['persentase'], 1, ',', '.') }}%</td></tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">Laporan Sawitku · dicetak {{ $printedAt->format('d/m/Y H:i') }}</div>
</body>
</html>
