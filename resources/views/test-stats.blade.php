<!DOCTYPE html>
<html>
<head>
    <title>Test Statistics</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .stats-box { border: 1px solid #ddd; padding: 20px; margin: 10px 0; background: #f9f9f9; }
        button { padding: 10px 15px; margin: 5px; cursor: pointer; }
        .debug { background: #ffe; border: 1px solid #dd0; padding: 10px; margin: 10px 0; }
        .error { background: #fee; border: 1px solid #d00; padding: 10px; margin: 10px 0; }
        .success { background: #efe; border: 1px solid #0d0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test Halaman Statistik Transaksi</h1>
    
    <div>
        <button onclick="testDatabase()">Test Database Connection</button>
        <button onclick="createTestData()">Create Test Data</button>
        <button onclick="loadStatistics()">Load Statistics</button>
    </div>

    <div id="result" class="debug">Klik tombol untuk test...</div>

    <div class="stats-box">
        <h3>Statistik Penjualan</h3>
        <div>Penjualan Hari Ini: <span id="penjualan-hari-ini">-</span></div>
        <div>Transaksi Hari Ini: <span id="transaksi-hari-ini">-</span></div>
        <div>Penjualan Bulan Ini: <span id="penjualan-bulan-ini">-</span></div>
        <div>Total Pelanggan: <span id="total-pelanggan">-</span></div>
    </div>

    <script>
        function showResult(data, type = 'debug') {
            const resultDiv = document.getElementById('result');
            resultDiv.className = type;
            resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        }

        function testDatabase() {
            $.get('/test-db')
                .done(function(data) {
                    showResult(data, 'success');
                })
                .fail(function(xhr) {
                    showResult({
                        error: 'Request failed',
                        status: xhr.status,
                        response: xhr.responseText
                    }, 'error');
                });
        }

        function createTestData() {
            $.get('/create-test-data')
                .done(function(data) {
                    showResult(data, 'success');
                })
                .fail(function(xhr) {
                    showResult({
                        error: 'Request failed',
                        status: xhr.status,
                        response: xhr.responseText
                    }, 'error');
                });
        }

        function loadStatistics() {
            $.get('/debug-stats')
                .done(function(data) {
                    showResult(data, 'success');
                    
                    if (data.status === 'success' && data.data) {
                        $('#penjualan-hari-ini').text(data.data.penjualan_hari_ini || '0');
                        $('#transaksi-hari-ini').text(data.data.transaksi_hari_ini || '0');
                        $('#penjualan-bulan-ini').text(data.data.penjualan_bulan_ini || '0');
                        $('#total-pelanggan').text(data.data.total_pelanggan || '0');
                    }
                })
                .fail(function(xhr) {
                    showResult({
                        error: 'Request failed',
                        status: xhr.status,
                        response: xhr.responseText
                    }, 'error');
                });
        }
    </script>
</body>
</html>
