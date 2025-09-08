# ROTI CRUD SYSTEM - SIMPLIFIED VERSION ✅ COMPLETED

## System Overview
CRUD roti yang sudah disederhanakan dengan hanya field **nama** dan **harga**, mengikuti pattern yang sama dengan menu users.

## Database Schema (rotis table)
```sql
id (bigint, primary key, auto increment)
nama (varchar 255) 
harga (decimal 10,2)
deleted_at (timestamp, nullable) -- for soft deletes
created_at (timestamp)
updated_at (timestamp)
```

## Model Configuration
**File**: `app/Models/Roti.php`
```php
protected $fillable = [
    'nama',
    'harga'
];
```

## Controller Structure
**File**: `app/Http/Controllers/RotiController.php`
- ✅ `index()` - Tampilkan daftar roti
- ✅ `show($id)` - Detail roti (JSON response) 
- ✅ `create()` - Form tambah roti
- ✅ `store(Request $request)` - Simpan roti baru
- ✅ `edit($id)` - Data untuk edit (JSON response)
- ✅ `update(Request $request, $id)` - Update roti
- ✅ `destroy($id)` - Hapus roti

## View Structure 
**File**: `resources/views/roti/index.blade.php`

### Features:
- ✅ Simple table layout (mirip users): No, Nama Roti, Harga, Aksi
- ✅ Modal form untuk tambah/edit (sama seperti users)
- ✅ Modal konfirmasi untuk hapus
- ✅ AJAX operations (tidak reload halaman penuh)
- ✅ Alertify notifications untuk feedback
- ✅ Responsive design dengan gradient styling
- ✅ Form validation dengan error handling

### UI Components:
- **Breadcrumb**: Dashboard → Data Roti
- **Header**: "Manajemen Data Roti" dengan tombol "Tambah Roti"
- **Table**: Simple 4-column layout
- **Buttons**: Edit (warning), Hapus (danger)
- **Modals**: Tambah/Edit, Konfirmasi Hapus

## Routes (Laravel Resource)
```
GET     /admin/roti ................. admin_roti.index
POST    /admin/roti ................. admin_roti.store  
GET     /admin/roti/create .......... admin_roti.create
GET     /admin/roti/{roti} .......... admin_roti.show
PUT     /admin/roti/{roti} .......... admin_roti.update
DELETE  /admin/roti/{roti} .......... admin_roti.destroy
GET     /admin/roti/{roti}/edit ..... admin_roti.edit
```

## Sample Data
```
1. Roti Tawar - Rp 5.000
2. Roti Manis - Rp 3.000  
3. Roti Coklat - Rp 7.000
4. Roti Keju - Rp 8.000
5. Roti Pisang - Rp 6.000
```

## Validation Rules
```php
'nama' => 'required|string|max:255'
'harga' => 'required|numeric|min:0'
```

## Access & Testing
- **URL**: http://127.0.0.1:8000/admin/roti
- **Role**: Admin only (protected by AdminMiddleware)
- **Menu**: Sidebar → "Data Roti" (sudah tersedia)

### Test Scenarios:
1. ✅ **Tambah Roti**: Klik "Tambah Roti", isi form, submit
2. ✅ **Edit Roti**: Klik tombol "Edit", ubah data, update
3. ✅ **Hapus Roti**: Klik tombol "Hapus", konfirmasi
4. ✅ **Validation**: Submit form kosong/invalid data
5. ✅ **Responsive**: Test di mobile/tablet

## Technical Stack
- **Backend**: Laravel 11 (Resource Controller)
- **Frontend**: Bootstrap 4 + jQuery
- **Database**: MySQL (dengan soft deletes)
- **Notifications**: Alertify.js
- **Authentication**: AdminMiddleware protection

## Status: ✅ FULLY OPERATIONAL
Sistem CRUD roti sudah sepenuhnya berfungsi dengan desain yang konsisten dengan menu users, hanya menggunakan field nama dan harga sesuai permintaan.

## Key Differences from Complex Version:
- ❌ Removed: kategori, stok, deskripsi, status fields
- ❌ Removed: DataTables server-side processing
- ✅ Added: Simple table with page reload (like users)
- ✅ Added: Consistent modal design
- ✅ Added: Same button styling and layout

**Ready for production use! 🎉**
