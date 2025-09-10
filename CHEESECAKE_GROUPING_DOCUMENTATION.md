# Dokumentasi Implementasi Fitur Grouping Cheesecake

## Overview
Fitur grouping telah diimplementasikan pada menu cheesecake untuk menggabungkan data yang memiliki:
- **Tanggal produksi yang sama** (`tanggal_dibuat`)
- **Roti ID yang sama** (`roti_id`)

## Fitur yang Ditambahkan

### 1. Method Grouping di Controller
- `groupCheesecakeData()` - Method untuk mengelompokkan data cheesecake
- `getGroupDetails()` - Method untuk menampilkan detail items dalam grup

### 2. Modifikasi DataTable
- Kolom "Jumlah" menampilkan total jumlah untuk grup + jumlah entries
- Kolom "Kode Produk" menampilkan badge jumlah items dalam grup
- Kolom "Nama" menandai data yang di-grup dengan label "(Grouped)"
- Kolom "Harga" menampilkan harga rata-rata untuk grup
- Kolom "Total" menampilkan total nilai keseluruhan grup

### 3. Action Buttons
- **Toggle Group Button** (ikon +) - Untuk grup dengan lebih dari 1 item
- **View Group Details Button** (ikon eye) - Menampilkan detail modal
- **Edit/Delete Buttons** - Hanya untuk single items

### 4. Modal Group Details
- **Summary Cards** - Menampilkan statistik grup (Total Items, Total Jumlah, Total Nilai, Harga Rata-rata)
- **Detail Table** - Menampilkan semua items dalam grup dengan informasi lengkap

## Perubahan pada File

### 1. Controller (`app/Http/Controllers/CheesecakeController.php`)
```php
// Method baru yang ditambahkan:
- groupCheesecakeData($data)      // Logic grouping utama
- getGroupDetails(Request $request, $groupId) // API endpoint untuk detail grup

// Modifikasi pada method index():
- Menggunakan groupCheesecakeData untuk memproses data
- DataTables columns diubah untuk mendukung format grup
```

### 2. Routes (`routes/web.php`)
```php
// Route baru ditambahkan untuk semua role:
Route::get('/cheesecake/group/{groupId}', [CheesecakeController::class, 'getGroupDetails'])
    ->name('cheesecake_group_details');
```

### 3. View (`resources/views/cheesecake/index.blade.php`)
```html
<!-- Modal baru ditambahkan: -->
<div class="modal fade" id="group-details-modal">
  <!-- Modal untuk menampilkan detail grup -->
</div>

<!-- JavaScript baru: -->
- Event handler untuk tombol view group details
- Function untuk menampilkan modal group details
- Update DataTable columns untuk mendukung grouping
```

## Logika Grouping

### 1. Group Key Generation
```php
$groupKey = $item->tanggal_dibuat->format('Y-m-d') . '_' . $item->roti_id;
```
Contoh: `2025-09-09_6` (tanggal 2025-09-09, roti_id = 6)

### 2. Perhitungan untuk Group
- **Total Jumlah**: `$items->sum('jumlah')`
- **Total Nilai**: `$items->sum(function ($item) { return $item->jumlah * $item->harga; })`
- **Harga Rata-rata**: `$items->avg('harga')`
- **Jumlah Items**: `$items->count()`

### 3. Data Structure untuk DataTable
```php
[
    'id' => $firstItem->id,
    'group_id' => $groupKey,
    'is_grouped' => true/false,
    'group_count' => $items->count(),
    'group_items' => $items,
    'total_jumlah' => $totalJumlah,
    'total_nilai' => $totalNilai,
    'avg_harga' => $avgHarga,
    // ... fields lainnya
]
```

## Tampilan UI

### 1. Tabel Utama
- **Single Item**: Ditampilkan normal tanpa perubahan
- **Grouped Item**: 
  - Badge biru menunjukkan jumlah items dalam grup
  - Label "(Grouped)" pada nama produk
  - Label "(Group)" pada nama baker
  - Harga ditampilkan dengan label "(avg)" untuk rata-rata
  - Jumlah ditampilkan dengan format: `33 (5 entries)`

### 2. Action Buttons
- **Normal Item**: Edit, Delete, View Detail, QR Code
- **Grouped Item**: Toggle Group, View Group Details

### 3. Group Details Modal
```
┌─────────────────────────────────────────────────────┐
│ Detail Group Produksi                               │
├─────────────────────────────────────────────────────┤
│ [Total Items: 5] [Total Jumlah: 58] [Total Nilai]  │
│                  [Harga Rata-rata]                  │
├─────────────────────────────────────────────────────┤
│ Info Group: Roti Tawar Test                        │
│ Tanggal Produksi: 09-09-2025                      │
├─────────────────────────────────────────────────────┤
│ Tabel detail dengan semua items dalam grup         │
└─────────────────────────────────────────────────────┘
```

## Benefit dari Grouping

### 1. **Pengurangan Clutter**
- Data dengan tanggal dan jenis roti sama tidak memenuhi tabel
- Tampilan lebih clean dan organized

### 2. **Informasi Agregat**
- Total produksi per hari per jenis roti
- Harga rata-rata untuk analisis
- Total nilai produksi yang mudah dilihat

### 3. **Keterangan Penambahan Data**
- Badge menunjukkan berapa kali penambahan data dilakukan
- Label "(Grouped)" memberikan indikasi visual data yang digabung
- Modal detail memberikan breakdown lengkap

### 4. **Fleksibilitas**
- Data single tetap dapat diedit/delete individual
- Data grup dapat di-expand untuk melihat detail
- Tidak mengubah fungsi CRUD yang sudah ada

## Testing

Test berhasil dilakukan dengan:
- 5 items dengan tanggal dan roti_id yang sama → Tergabung dalam 1 grup
- 3 items dengan kombinasi tanggal/roti_id berbeda → Tetap terpisah

Result grouping:
```
Group: 2025-09-09_6 (5 items)
- Total Jumlah: 58
- Total Nilai: Rp 291,600  
- Harga Rata-rata: Rp 5,040
```

## Cara Penggunaan

1. **Melihat Data Grouped**: Data dengan badge biru menandakan grup
2. **Melihat Detail Grup**: Klik tombol mata (eye) pada data yang ter-grup
3. **Memahami Summary**: Modal akan menampilkan ringkasan grup dan detail items

Fitur ini meningkatkan user experience dengan memberikan view yang lebih terorganisir tanpa menghilangkan detail informasi.
