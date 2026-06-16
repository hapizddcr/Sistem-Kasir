<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $sale->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 8px; }
        .text-center { text-align: center; }
        .row { display: flex; justify-content: space-between; }
        hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; font-size: 11px; }
        @media print {
            body { width: auto; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h3 style="margin: 0;">{{ config('app.name') }}</h3>
        <small>Jl. Contoh No. 123, Jakarta<br>Telp: 021-12345678</small>
    </div>
    <hr>
    <div class="row"><span>No:</span><span>{{ $sale->invoice_number }}</span></div>
    <div class="row"><span>Tgl:</span><span>{{ $sale->sale_date->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Kasir:</span><span>{{ $sale->user->name }}</span></div>
    <div class="row"><span>Cust:</span><span>{{ $sale->customer->name ?? 'Umum' }}</span></div>
    <hr>
    <table>
        @foreach ($sale->items as $item)
        <tr>
            <td colspan="2">{{ $item->product_name }}</td>
        </tr>
        <tr>
            <td>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</td>
            <td style="text-align: right;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    <hr>
    <div class="row"><span>Subtotal:</span><span>{{ number_format($sale->subtotal, 0, ',', '.') }}</span></div>
    @if ($sale->discount > 0)
    <div class="row"><span>Diskon:</span><span>-{{ number_format($sale->discount, 0, ',', '.') }}</span></div>
    @endif
    <div class="row"><span>Pajak:</span><span>{{ number_format($sale->tax, 0, ',', '.') }}</span></div>
    <div class="row" style="font-size: 14px; font-weight: bold;"><span>TOTAL:</span><span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span></div>
    <hr>
    @foreach ($sale->payments as $p)
    <div class="row"><span>{{ ucfirst($p->method) }}:</span><span>{{ number_format($p->amount, 0, ',', '.') }}</span></div>
    @endforeach
    <div class="row"><span>Kembali:</span><span>{{ number_format($sale->change_amount, 0, ',', '.') }}</span></div>
    @if ($sale->points_earned > 0)
    <hr>
    <div class="text-center"><small>Poin didapat: <b>+{{ $sale->points_earned }}</b></small></div>
    @endif
    <hr>
    <div class="text-center">
        <small>Terima kasih atas kunjungan Anda<br>{{ now()->format('d/m/Y H:i:s') }}</small>
    </div>
    <div class="no-print text-center mt-3">
        <button onclick="window.print()" style="padding: 8px 16px;">🖨 Cetak</button>
        <button onclick="window.close()" style="padding: 8px 16px;">Tutup</button>
    </div>
</body>
</html>
