# CRUD Roti - Dokumentasi

## Overview
CRUD untuk data roti telah dibuat dengan template yang konsisten dengan menu users. CRUD ini memiliki fitur lengkap untuk mengelola data roti.

## Features
- ✅ **Create**: Tambah roti baru
- ✅ **Read**: Lihat daftar roti dengan DataTables
- ✅ **Update**: Edit data roti
- ✅ **Delete**: Hapus roti (soft delete)

## Struktur Database
Tabel: `rotis`
- `id` (Primary Key)
- `nama_roti` (VARCHAR 255)
- `harga` (TEXT) 
- `created_at` (Timestamp)
- `updated_at` (Timestamp)
- `deleted_at` (Timestamp, nullable) - untuk soft delete

## Files Created/Modified

### 1. Model
- `app/Models/Roti.php` - Model dengan fillable dan soft delete

### 2. Controller
- `app/Http/Controllers/RotiController.php` - Controller lengkap dengan validation

### 3. Views
- `resources/views/roti/index.blade.php` - Halaman utama CRUD dengan modal

### 4. Routes
- Route resource sudah ada: `Route::resource('roti', RotiController::class);`
- Prefix: `admin/`
- Middleware: `AdminMiddleware`

### 5. Menu
- Menu ditambahkan di sidebar untuk role admin
- Icon: `mdi-bread-slice`

### 6. Database
- Migration: `2025_09_07_142936_create_rotis_table.php`
- Migration: `2025_09_07_145420_add_deleted_at_to_rotis_table.php`
- Seeder: `database/seeders/RotiSeeder.php` (5 data sample)

## Cara Menggunakan

### 1. Login sebagai Admin
- Username: `admin`
- Password: `password`

### 2. Akses Menu
- Sidebar → "Data Roti" (icon roti)
- URL: `http://localhost:8000/admin/roti`

### 3. Operasi CRUD

#### Tambah Roti
1. Klik tombol "Tambah Roti"
2. Isi form:
   - Nama Roti (required)
   - Harga (required, numeric, min: 0)
3. Klik "Simpan"

#### Edit Roti
1. Klik icon pensil (edit) pada baris data
2. Form akan terisi otomatis
3. Ubah data yang diinginkan
4. Klik "Update"

#### Hapus Roti
1. Klik icon trash (hapus) pada baris data
2. Konfirmasi penghapusan
3. Data akan dihapus (soft delete)

## Features Detail

### UI/UX
- ✅ Responsive design
- ✅ Modal untuk form (tidak pindah halaman)
- ✅ DataTables dengan pagination, search, sorting
- ✅ Gradient theme konsisten dengan template
- ✅ Icon yang sesuai (roti)
- ✅ Notifikasi Alertify
- ✅ Loading states

### Validasi
- ✅ Server-side validation
- ✅ Client-side validation
- ✅ Error messages dalam bahasa Indonesia
- ✅ Required field indicators

### Keamanan
- ✅ CSRF protection
- ✅ Middleware authentication (admin only)
- ✅ Input sanitization
- ✅ Soft delete (data tidak benar-benar terhapus)

## API Routes Available
```
GET    /admin/roti           - List roti (DataTables AJAX)
POST   /admin/roti           - Create roti
GET    /admin/roti/{id}      - Show roti
PUT    /admin/roti/{id}      - Update roti
DELETE /admin/roti/{id}      - Delete roti
GET    /admin/roti/{id}/edit - Get roti for editing
```

## Sample Data
5 data roti telah dibuat via seeder:
1. Roti Tawar - Rp 5.000
2. Roti Manis - Rp 3.000
3. Roti Coklat - Rp 7.000
4. Roti Keju - Rp 8.000
5. Roti Pisang - Rp 6.000

## Testing
1. Jalankan server: `php artisan serve`
2. Login sebagai admin
3. Akses `/admin/roti`
4. Test semua operasi CRUD

Semua fitur CRUD roti telah dibuat dan siap digunakan! 🍞✨
