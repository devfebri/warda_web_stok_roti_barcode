<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title') - {{ $company['name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 10px;
        }
        
        .report-period {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        
        .summary-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        
        .summary-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .table-container {
            margin: 20px 0;
        }
        
        .table-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        
        td {
            font-size: 9px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .footer-info {
            margin-bottom: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .currency {
            font-family: monospace;
        }
        
        .highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .chart-placeholder {
            width: 100%;
            height: 200px;
            border: 1px dashed #ddd;
            text-align: center;
            line-height: 200px;
            color: #666;
            margin: 20px 0;
        }
        
        @media print {
            body {
                font-size: 11px;
            }
            
            .summary-grid {
                break-inside: avoid;
            }
            
            .table-container {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-info">
            {{ $company['address'] }}<br>
            Telp: {{ $company['phone'] }} | Email: {{ $company['email'] }}
        </div>
        <div class="report-title">@yield('title')</div>
        <div class="report-period">@yield('period')</div>
    </div>

    @yield('content')

    <div class="footer">
        <div class="footer-info">
            Laporan ini digenerate secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <div>{{ $company['name'] }} - Sistem Manajemen Bakery</div>
    </div>
</body>
</html>
