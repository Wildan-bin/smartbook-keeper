<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan {{ $monthLabel }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 10px; 
            margin: 20px; 
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            border-bottom: 3px solid #007abb; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
            background: linear-gradient(135deg, #007abb, #2FABEC);
            color: white;
            padding: 20px;
            border-radius: 8px;
        }
        .summary { 
            display: flex; 
            justify-content: space-between; 
            margin: 25px 0; 
        }
        .summary-item { 
            flex: 1; 
            text-align: center; 
            padding: 15px; 
            border: 1px solid #ddd; 
            margin: 0 5px; 
            background: #f8f9fa;
            border-radius: 6px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
            background: white;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
            font-size: 9px; 
        }
        th { 
            background: #f0f0f0; 
            text-align: center; 
            font-weight: bold;
        }
        .amount-income { 
            color: #22c55e; 
            text-align: right; 
            font-weight: bold;
        }
        .amount-expense { 
            color: #ef4444; 
            text-align: right; 
            font-weight: bold;
        }
        .table-title {
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #007abb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 SmartBook Keeper</h1>
        <h2>Laporan Keuangan {{ $monthLabel }}</h2>
        <p>{{ $periodStart }} - {{ $periodEnd }} | {{ $user->name }}</p>
        <p style="font-size: 8px; margin-top: 10px;">Dicetak pada: {{ $generatedAt }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-item">
            <strong>💰 Pemasukan</strong><br>
            <span style="color: #22c55e; font-size: 12px; font-weight: bold;">
                Rp {{ number_format($summary['totalIncome'], 0, ',', '.') }}
            </span>
        </div>
        <div class="summary-item">
            <strong>💸 Pengeluaran</strong><br>
            <span style="color: #ef4444; font-size: 12px; font-weight: bold;">
                Rp {{ number_format($summary['totalExpense'], 0, ',', '.') }}
            </span>
        </div>
        <div class="summary-item">
            <strong>🏦 Saldo Bersih</strong><br>
            @php
                $balanceColor = $summary['netBalance'] >= 0 ? '#22c55e' : '#ef4444';
            @endphp
            <span style=" font-size: 12px; font-weight: bold;">
                Rp {{ number_format($summary['netBalance'], 0, ',', '.') }}
            </span>
        </div>
    </div>
    
    @if(count($incomeTransactions) > 0)
    <div class="table-title">📈 Pemasukan ({{ count($incomeTransactions) }} transaksi)</div>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Sumber</th>
            <th>Deskripsi</th>
        </tr>
        @foreach($incomeTransactions as $t)
        <tr>
            <td>{{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</td>
            <td>{{ $t->category->name ?? '-' }}</td>
            <td class="amount-income">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
            <td>{{ $t->balance->name ?? '-' }}</td>
            <td>{{ $t->description ?: '-' }}</td>
        </tr>
        @endforeach
        <tr style="background: #f0f0f0; font-weight: bold;">
            <td colspan="2" style="text-align: center;">TOTAL</td>
            <td class="amount-income">Rp {{ number_format($summary['totalIncome'], 0, ',', '.') }}</td>
            <td colspan="2"></td>
        </tr>
    </table>
    @endif
    
    @if(count($expenseTransactions) > 0)
    <div class="table-title">📉 Pengeluaran ({{ count($expenseTransactions) }} transaksi)</div>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Sumber</th>
            <th>Deskripsi</th>
        </tr>
        @foreach($expenseTransactions as $t)
        <tr>
            <td>{{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</td>
            <td>{{ $t->category->name ?? '-' }}</td>
            <td class="amount-expense">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
            <td>{{ $t->balance->name ?? '-' }}</td>
            <td>{{ $t->description ?: '-' }}</td>
        </tr>
        @endforeach
        <tr style="background: #f0f0f0; font-weight: bold;">
            <td colspan="2" style="text-align: center;">TOTAL</td>
            <td class="amount-expense">Rp {{ number_format($summary['totalExpense'], 0, ',', '.') }}</td>
            <td colspan="2"></td>
        </tr>
    </table>
    @endif
    
    @if(count($incomeTransactions) === 0 && count($expenseTransactions) === 0)
    <div style="text-align: center; padding: 50px; color: #666;">
        <h3>Tidak Ada Data</h3>
        <p>Belum ada transaksi untuk periode {{ $monthLabel }}</p>
    </div>
    @endif

    <div class="footer">
        <div><strong>SmartBook Keeper</strong> - Sistem Pengelolaan Keuangan Digital</div>
        <div>© {{ date('Y') }} SmartBook Keeper. Dokumen ini bersifat rahasia dan hanya untuk penggunaan internal.</div>
    </div>
</body>
</html>