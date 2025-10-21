# 🌳 Data KTB - Sistem Pendataan Pohon Kelompok Tumbuh Bersama

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL-orange.svg)](https://mysql.com)
[![Status](https://img.shields.io/badge/Status-Ready-green.svg)]()

Sistem manajemen dan tracking pohon hierarki KTB (Kelompok Tumbuh Bersama) dengan fitur multiplikasi dan relasi mentor-mentee.

---

## 📖 Deskripsi

Sistem ini dirancang untuk melakukan pendataan struktur pohon KTB berdasarkan profil seseorang. Contohnya:
- **Andi** memiliki 4 adik KTB: A, B, C, dan D
- **A** bermultiplikasi dan membuka Kelompok KTB baru dengan 3 adik KTB: E, F, dan G
- **B** juga bermultiplikasi dengan 2 adik KTB: H dan I

Sistem dapat mendata:
✅ Berapa kelompok yang dimiliki seseorang
✅ Berapa adik KTB dalam setiap kelompok
✅ Siapa saja yang sudah bermultiplikasi (membuka kelompok baru)
✅ Struktur pohon hierarki lengkap secara rekursif

---

## ✨ Fitur Utama

### 🎯 Core Features
- **Manajemen Anggota KTB** - CRUD lengkap dengan profil detail
- **Manajemen Kelompok** - Tracking kelompok dengan jadwal pertemuan
- **Relasi Mentor-Mentee** - System tracking Kakak KTB ↔ Adik KTB
- **Multiplikasi Tracking** - Monitor siapa yang sudah membuka kelompok baru
- **Generation System** - Automatic tracking generasi (1, 2, 3, ...)

### 📊 Advanced Features
- **Tree Structure** - Visualisasi pohon hierarki lengkap
- **Recursive Queries** - Mendapatkan semua keturunan secara otomatis
- **Statistics** - Laporan multiplikasi rate, growth rate, dll
- **Soft Deletes** - Data history terjaga dengan soft delete
- **Status Management** - Multi-status untuk member, group, dan relationship

---

## 🗄️ Database Schema

```
users ──(0..1)── ktb_members ──(*)── ktb_groups
                      │
                      └──► Self-Referencing Relationship
                           (mentor_id ↔ mentee_id)
                           via ktb_member_relationships
```

### 3 Tabel Utama:
1. **ktb_members** - Data anggota KTB
2. **ktb_groups** - Data kelompok KTB  
3. **ktb_member_relationships** - Relasi hierarki (pivot table)

📚 [Lihat ERD Lengkap](docs/ERD_DETAILED.md)

---

## 🚀 Quick Start

### Prerequisites
- PHP >= 8.3
- Composer
- MySQL/MariaDB
- Laravel 11.x

### Installation

```bash
# 1. Clone repository
git clone <repository-url>
cd data-ktb

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_ktb
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. (Optional) Seed sample data
php artisan db:seed --class=KtbSeeder

# 7. Verify installation
php artisan tinker test_ktb_data.php
```

---

## 📊 Sample Data

Setelah menjalankan seeder, database akan berisi:

```
ANDI (Generation 1)
├── A (Gen 2) → Opens Kelompok A
│   ├── E (Gen 3)
│   ├── F (Gen 3)
│   └── G (Gen 3)
├── B (Gen 2) → Opens Kelompok B
│   ├── H (Gen 3)
│   └── I (Gen 3)
├── C (Gen 2)
└── D (Gen 2)
```

**Stats:** 3 kelompok, 10 anggota, 9 relasi, 2 multiplikasi

---

## 💻 Contoh Penggunaan

### Mendapatkan Adik KTB
```php
$andi = KtbMember::where('name', 'Andi')->first();
$adikKtb = $andi->mentees;
// Returns: A, B, C, D
```

### Cek Multiplikasi
```php
if ($member->hasOpenedNewGroup()) {
    echo "Sudah membuka kelompok baru!";
}
```

### Mendapatkan Semua Keturunan (Recursive)
```php
$descendants = $andi->getAllDescendants();
// Returns: A, B, C, D, E, F, G, H, I (9 members)
```

### Struktur Pohon Lengkap
```php
$tree = $andi->getTreeStructure();
// Returns hierarchical array with all levels
```

### Statistik Kelompok
```php
$group = KtbGroup::find(1);
$total = $group->members()->count();
$multipliers = $group->members()
    ->whereHas('leadingGroups')
    ->count();
$rate = ($multipliers / $total) * 100;
```

📚 [Lihat Lebih Banyak Contoh](docs/README_KTB.md#-penggunaan)

---

## 📚 Dokumentasi

### 📖 Available Documentation

| File | Description | Best For |
|------|-------------|----------|
| [📋 INDEX](docs/INDEX.md) | Navigation & overview | Start here |
| [⚡ QUICK_REFERENCE](docs/QUICK_REFERENCE.md) | Cheat sheet | Quick lookup |
| [✨ SUMMARY](docs/SUMMARY.md) | Summary & next steps | Overview |
| [📘 README_KTB](docs/README_KTB.md) | Complete guide | Full reference |
| [🗄️ DATABASE_DESIGN](docs/DATABASE_DESIGN.md) | DB design & queries | DB details |
| [📊 ERD_DETAILED](docs/ERD_DETAILED.md) | ERD diagram | Technical |
| [🌳 KTB_TREE_VISUALIZATION](docs/KTB_TREE_VISUALIZATION.md) | Tree visualization | Understanding data |
| [🎨 VISUAL_GUIDE](docs/VISUAL_GUIDE.md) | Visual concepts | UI design |

**👉 Start with:** [docs/INDEX.md](docs/INDEX.md) atau [docs/QUICK_REFERENCE.md](docs/QUICK_REFERENCE.md)

---

## 🏗️ Project Structure

```
data-ktb/
├── app/
│   └── Models/
│       ├── KtbGroup.php
│       ├── KtbMember.php
│       └── KtbMemberRelationship.php
├── database/
│   ├── migrations/
│   │   ├── *_create_ktb_groups_table.php
│   │   ├── *_create_ktb_members_table.php
│   │   ├── *_create_ktb_member_relationships_table.php
│   │   └── *_add_foreign_keys_to_ktb_tables.php
│   └── seeders/
│       └── KtbSeeder.php
├── docs/
│   ├── INDEX.md
│   ├── QUICK_REFERENCE.md
│   ├── SUMMARY.md
│   ├── README_KTB.md
│   ├── DATABASE_DESIGN.md
│   ├── ERD_DETAILED.md
│   ├── KTB_TREE_VISUALIZATION.md
│   └── VISUAL_GUIDE.md
├── test_ktb_data.php
└── README.md (this file)
```

---

## 🎯 Next Steps

Setelah database setup selesai, Anda bisa lanjut ke:

### 1. Backend Development
```bash
# Create controllers
php artisan make:controller Api/KtbMemberController --api
php artisan make:controller Api/KtbGroupController --api

# Create requests (validation)
php artisan make:request StoreKtbMemberRequest
php artisan make:request UpdateKtbMemberRequest

# Create resources (API response)
php artisan make:resource KtbMemberResource
php artisan make:resource KtbGroupResource

# Create policies (authorization)
php artisan make:policy KtbMemberPolicy --model=KtbMember
```

### 2. Frontend Development
- Dashboard overview
- Member management CRUD
- Group management CRUD
- Tree visualization component
- Statistics & reports

### 3. Testing
```bash
php artisan make:test KtbMemberTest
php artisan make:test KtbGroupTest
```

📚 [Lihat Detail Next Steps](docs/SUMMARY.md#-next-steps)

---

## 🧪 Testing

```bash
# Verify data
php artisan tinker test_ktb_data.php

# Run tests (if created)
php artisan test
```

---

## 🛠️ Tech Stack

- **Framework:** Laravel 11.x
- **PHP:** 8.3+
- **Database:** MySQL/MariaDB
- **ORM:** Eloquent
- **Features:** Soft Deletes, Eager Loading, Recursive Relationships

---

## 📊 Database Stats

✅ **3 Tables** (ktb_members, ktb_groups, ktb_member_relationships)  
✅ **3 Models** with rich relationships  
✅ **10+ Helper Methods** for common operations  
✅ **Fully Documented** with examples  
✅ **Sample Data** included in seeder  
✅ **Recursive Queries** support  

---

## 💡 Key Features Highlight

### Self-Referencing Relationship
```php
// One member can have many mentees (adik KTB)
$member->mentees; // Returns collection of KtbMember

// One member can have many mentors (kakak KTB)
$member->mentors; // Returns collection of KtbMember
```

### Recursive Tree Structure
```php
// Get all descendants recursively
$allDescendants = $member->getAllDescendants();

// Get hierarchical tree structure
$tree = $member->getTreeStructure();
```

### Multiplication Tracking
```php
// Check if member has opened new group
$hasMultiplied = $member->hasOpenedNewGroup();

// Get all groups member is leading
$leadingGroups = $member->leadingGroups;
```

---

## 📞 Support

Untuk pertanyaan atau bantuan:
1. 📖 Cek [dokumentasi lengkap](docs/INDEX.md)
2. ⚡ Lihat [quick reference](docs/QUICK_REFERENCE.md)
3. 🐛 Report issues di repository

---

## 📝 License

[Specify your license here]

---

## 👨‍💻 Author

[Your name/organization]

---

## 🙏 Acknowledgments

Dibuat untuk komunitas KTB (Kelompok Tumbuh Bersama) untuk memudahkan tracking multiplikasi dan pertumbuhan kelompok.

---

**⭐ Star this repo if you find it useful!**

**📅 Last Updated:** October 21, 2025  
**🏷️ Version:** 1.0.0  
**✅ Status:** Production Ready
