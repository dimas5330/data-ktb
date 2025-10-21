# 📚 Dokumentasi Sistem Pendataan Pohon KTB

## 📖 Daftar Isi

### 1. [SUMMARY.md](SUMMARY.md) ⭐ **START HERE**
Ringkasan lengkap tentang apa yang telah dibuat, status database, dan next steps.

**Isi:**
- ✅ Daftar file yang telah dibuat
- ✅ Struktur database singkat
- ✅ Status migration & seeder
- ✅ Contoh data yang di-seed
- ✅ Langkah selanjutnya (Controllers, Routes, dll)

---

### 2. [DATABASE_DESIGN.md](DATABASE_DESIGN.md)
Dokumentasi lengkap tentang desain database, relasi, dan cara penggunaannya.

**Isi:**
- Entity Relationship Diagram (ERD)
- Detail tabel dan kolom
- Penjelasan relasi antar tabel
- Contoh query lengkap
- API methods dari Model
- Contoh kasus penggunaan

---

### 3. [ERD_DETAILED.md](ERD_DETAILED.md)
Diagram ERD detail dengan penjelasan teknis mendalam.

**Isi:**
- Diagram ERD visual ASCII
- Kardinalitas relasi
- Foreign keys & constraints
- Indexes untuk performa
- Normalisasi database (3NF)
- Scalability considerations
- Query optimization tips

---

### 4. [KTB_TREE_VISUALIZATION.md](KTB_TREE_VISUALIZATION.md)
Visualisasi pohon hierarki KTB dengan contoh data Andi.

**Isi:**
- Struktur hierarki visual
- Detail per kelompok
- Statistik pertumbuhan
- Timeline pertumbuhan
- Analisis multiplikasi
- Contoh query untuk mendapatkan data tree

---

### 5. [README_KTB.md](README_KTB.md)
Guide lengkap penggunaan sistem KTB dari instalasi hingga implementasi.

**Isi:**
- Overview sistem
- Fitur-fitur utama
- Instalasi step-by-step
- Penggunaan & contoh kode
- Query dasar & lanjutan
- Contoh laporan
- API endpoints
- Testing
- Notes & best practices

---

## 🗂️ Struktur File Project

```
data-ktb/
├── app/
│   └── Models/
│       ├── KtbGroup.php           ← Model kelompok KTB
│       ├── KtbMember.php          ← Model anggota KTB
│       └── KtbMemberRelationship.php  ← Model relasi mentoring
│
├── database/
│   ├── migrations/
│   │   ├── 2025_10_21_141637_create_ktb_groups_table.php
│   │   ├── 2025_10_21_141700_create_ktb_members_table.php
│   │   ├── 2025_10_21_141720_create_ktb_member_relationships_table.php
│   │   └── 2025_10_21_142901_add_foreign_keys_to_ktb_tables.php
│   │
│   └── seeders/
│       └── KtbSeeder.php          ← Sample data
│
├── docs/
│   ├── INDEX.md                   ← This file
│   ├── SUMMARY.md                 ← Quick summary
│   ├── DATABASE_DESIGN.md         ← Full database design
│   ├── ERD_DETAILED.md            ← ERD diagram
│   ├── KTB_TREE_VISUALIZATION.md  ← Tree visualization
│   └── README_KTB.md              ← Complete guide
│
└── test_ktb_data.php              ← Test script
```

---

## 🚀 Quick Start

### 1. Setup Database
```bash
# Jalankan migration
php artisan migrate

# Seed sample data
php artisan db:seed --class=KtbSeeder
```

### 2. Verifikasi Data
```bash
# Jalankan test script
php artisan tinker test_ktb_data.php
```

### 3. Mulai Development
Lihat [SUMMARY.md](SUMMARY.md) untuk next steps.

---

## 📊 Ringkasan Database

### 3 Tabel Utama:

1. **ktb_members**
   - Menyimpan data anggota KTB
   - Self-referencing untuk relasi hierarki
   - Tracking generation (1, 2, 3, ...)

2. **ktb_groups**
   - Menyimpan data kelompok KTB
   - Link ke leader (ktb_member)
   - Info pertemuan (lokasi, hari, waktu)

3. **ktb_member_relationships**
   - Pivot table untuk relasi mentor-mentee
   - Menyimpan relasi Kakak KTB ↔ Adik KTB
   - Tracking status relasi mentoring

### Relasi:
```
users ──(0..1)── ktb_members ──(*)── ktb_groups
                      │
                      └──► Self-Referencing (mentor ↔ mentee)
```

---

## 🎯 Use Cases

### 1. Tracking Multiplikasi
```php
$member = KtbMember::find(1);

// Cek sudah multiplikasi?
if ($member->hasOpenedNewGroup()) {
    echo "Sudah membuka kelompok baru!";
}

// Berapa kelompok yang dipimpin?
$totalGroups = $member->leadingGroups()->count();
```

### 2. Mendapatkan Struktur Pohon
```php
$rootMember = KtbMember::where('generation', 1)->first();

// Struktur hierarki lengkap
$tree = $rootMember->getTreeStructure();

// Semua keturunan (recursive)
$descendants = $rootMember->getAllDescendants();
```

### 3. Statistik Kelompok
```php
$group = KtbGroup::find(1);

// Total anggota
$totalMembers = $group->members()->count();

// Yang sudah multiplikasi
$multipliers = $group->members()
    ->whereHas('leadingGroups')
    ->get();

// Persentase multiplikasi
$rate = ($multipliers->count() / $totalMembers) * 100;
```

---

## 📈 Sample Data

Setelah seed, database berisi:

```
ANDI (Gen-1, Kelompok Andi)
├── A (Gen-2) → Kelompok A
│   ├── E (Gen-3)
│   ├── F (Gen-3)
│   └── G (Gen-3)
├── B (Gen-2) → Kelompok B
│   ├── H (Gen-3)
│   └── I (Gen-3)
├── C (Gen-2)
└── D (Gen-2)
```

**Stats:** 3 kelompok, 10 anggota, 9 relasi

---

## 🔗 Navigation

| Dokumen | Untuk Apa? | Prioritas |
|---------|-----------|-----------|
| [SUMMARY.md](SUMMARY.md) | Quick overview & next steps | ⭐⭐⭐ |
| [README_KTB.md](README_KTB.md) | Complete guide penggunaan | ⭐⭐⭐ |
| [DATABASE_DESIGN.md](DATABASE_DESIGN.md) | Detail desain & query | ⭐⭐ |
| [ERD_DETAILED.md](ERD_DETAILED.md) | Technical deep dive | ⭐ |
| [KTB_TREE_VISUALIZATION.md](KTB_TREE_VISUALIZATION.md) | Visualisasi data | ⭐⭐ |

---

## 💡 Tips

1. **Baca SUMMARY.md dulu** untuk overview cepat
2. **Gunakan README_KTB.md** sebagai panduan development
3. **Refer ke DATABASE_DESIGN.md** saat perlu detail query
4. **Lihat KTB_TREE_VISUALIZATION.md** untuk memahami struktur data

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Cek dokumentasi yang relevan
2. Lihat contoh query di DATABASE_DESIGN.md
3. Test dengan tinker: `php artisan tinker test_ktb_data.php`

---

**Last Updated:** October 21, 2025
**Database Version:** 1.0.0
**Status:** ✅ Production Ready
