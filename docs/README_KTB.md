# 🌳 Sistem Pendataan Pohon KTB (Kelompok Tumbuh Bersama)

## 📋 Deskripsi

Sistem ini dirancang untuk mengelola dan melacak struktur hierarki multiplikasi KTB (Kelompok Tumbuh Bersama). Sistem memungkinkan tracking relasi mentor-mentee (Kakak KTB - Adik KTB) secara rekursif, serta monitoring perkembangan multiplikasi kelompok.

## 🎯 Fitur Utama

### 1. **Manajemen Anggota KTB**
- Pendataan profil lengkap anggota
- Tracking generasi (Generation 1, 2, 3, dst)
- Status anggota (Active, Inactive, Alumni)
- Relasi dengan sistem user (optional)

### 2. **Manajemen Kelompok KTB**
- Pendataan kelompok dengan pemimpin
- Informasi pertemuan (lokasi, hari, waktu)
- Status kelompok (Active, Inactive, Completed)
- Tracking anggota per kelompok

### 3. **Relasi Mentoring (Kakak-Adik KTB)**
- Tracking relasi hierarki mentor-mentee
- Support multiplikasi (satu mentor → banyak mentee)
- History relasi mentoring
- Status relasi (Active, Inactive, Graduated)

### 4. **Visualisasi Pohon KTB**
- Struktur hierarki lengkap
- Total keturunan (descendants) secara rekursif
- Statistik multiplikasi per anggota
- Identifikasi anggota yang telah membuka kelompok baru

## 📊 Struktur Database

### Tabel Utama:

1. **ktb_members** - Data anggota KTB
2. **ktb_groups** - Data kelompok KTB
3. **ktb_member_relationships** - Relasi mentoring (pivot table)

### Relasi:

```
users ──(0..1)── ktb_members ──(*)── ktb_groups
                      │
                      │ Self-Referencing
                      │
                ktb_member_relationships
                (mentor_id ↔ mentee_id)
```

Dokumentasi lengkap:
- [Database Design](DATABASE_DESIGN.md)
- [ERD Detail](ERD_DETAILED.md)
- [Visualisasi Pohon KTB](KTB_TREE_VISUALIZATION.md)

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd data-ktb
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_ktb
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migration
```bash
php artisan migrate
```

### 6. (Optional) Seed Data Contoh
```bash
php artisan db:seed --class=KtbSeeder
```

Ini akan membuat data contoh:
- 1 Founder (Andi) dengan 4 adik KTB
- 2 kelompok multiplikasi dari adik KTB Andi
- Total 10 anggota dalam 3 kelompok

## 💻 Penggunaan

### Query Dasar

#### 1. Mendapatkan Adik KTB Langsung
```php
$member = KtbMember::find(1);
$adikKtb = $member->mentees;
```

#### 2. Mendapatkan Kakak KTB
```php
$member = KtbMember::find(2);
$kakakKtb = $member->mentors;
```

#### 3. Cek Apakah Sudah Membuka Kelompok Baru
```php
$member = KtbMember::find(2);
if ($member->hasOpenedNewGroup()) {
    echo "Sudah membuka kelompok baru!";
}
```

#### 4. Mendapatkan Semua Keturunan (Recursive)
```php
$member = KtbMember::find(1);
$allDescendants = $member->getAllDescendants();
// Return collection of all mentees, mentees of mentees, dst
```

#### 5. Mendapatkan Struktur Pohon
```php
$member = KtbMember::find(1);
$tree = $member->getTreeStructure();
// Return hierarchical array structure
```

#### 6. Statistik Kelompok
```php
$group = KtbGroup::find(1);

// Total anggota
$totalMembers = $group->members()->count();

// Anggota yang sudah multiplikasi
$multipliers = $group->members()
    ->whereHas('leadingGroups')
    ->get();

// Persentase multiplikasi
$multiplicationRate = ($multipliers->count() / $totalMembers) * 100;
```

### Contoh Implementasi

#### Membuat Anggota Baru
```php
$member = KtbMember::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '08123456789',
    'address' => 'Jakarta',
    'birth_date' => '1990-01-01',
    'gender' => 'male',
    'current_group_id' => 1,
    'is_leader' => false,
    'joined_at' => now(),
    'generation' => 2,
    'status' => 'active',
]);
```

#### Membuat Relasi Mentoring
```php
KtbMemberRelationship::create([
    'mentor_id' => 1, // Kakak KTB
    'mentee_id' => 2, // Adik KTB
    'group_id' => 1,
    'started_at' => now(),
    'status' => 'active',
]);

// Atau menggunakan relationship
$mentor = KtbMember::find(1);
$mentee = KtbMember::find(2);
$mentor->mentees()->attach($mentee->id, [
    'group_id' => 1,
    'started_at' => now(),
    'status' => 'active',
]);
```

#### Membuat Kelompok Baru (Multiplikasi)
```php
// Member A membuka kelompok baru
$member = KtbMember::find(2);
$member->update(['is_leader' => true]);

$newGroup = KtbGroup::create([
    'name' => 'Kelompok A',
    'leader_id' => $member->id,
    'description' => 'Kelompok multiplikasi dari Kelompok Andi',
    'location' => 'Jakarta',
    'meeting_day' => 'Kamis',
    'meeting_time' => '19:00:00',
    'status' => 'active',
    'started_at' => now(),
]);
```

## 📈 Contoh Laporan

### Laporan Statistik Keseluruhan
```php
// Total kelompok aktif
$activeGroups = KtbGroup::where('status', 'active')->count();

// Total anggota aktif
$activeMembers = KtbMember::where('status', 'active')->count();

// Total relasi mentoring aktif
$activeRelations = KtbMemberRelationship::where('status', 'active')->count();

// Distribusi per generasi
$genDistribution = KtbMember::groupBy('generation')
    ->selectRaw('generation, count(*) as total')
    ->get();

// Rate multiplikasi (member yang sudah buka kelompok / total member)
$totalMembers = KtbMember::count();
$leadersCount = KtbMember::where('is_leader', true)->count();
$multiplicationRate = ($leadersCount / $totalMembers) * 100;
```

### Laporan per Member
```php
$member = KtbMember::find(1);

$report = [
    'nama' => $member->name,
    'kelompok_saat_ini' => $member->currentGroup?->name,
    'total_adik_langsung' => $member->mentees()->count(),
    'total_adik_aktif' => $member->activeMentees()->count(),
    'total_keturunan' => $member->getAllDescendants()->count(),
    'sudah_multiplikasi' => $member->hasOpenedNewGroup(),
    'kelompok_yang_dipimpin' => $member->leadingGroups->pluck('name'),
    'generasi' => $member->generation,
];
```

## 🔍 API Endpoints (Contoh untuk Controller)

```php
// KtbMemberController
GET    /api/ktb-members              - List all members
GET    /api/ktb-members/{id}         - Get member detail
GET    /api/ktb-members/{id}/mentees - Get member's mentees
GET    /api/ktb-members/{id}/tree    - Get member's tree structure
POST   /api/ktb-members              - Create new member
PUT    /api/ktb-members/{id}         - Update member
DELETE /api/ktb-members/{id}         - Delete member

// KtbGroupController
GET    /api/ktb-groups               - List all groups
GET    /api/ktb-groups/{id}          - Get group detail
GET    /api/ktb-groups/{id}/members  - Get group members
POST   /api/ktb-groups               - Create new group
PUT    /api/ktb-groups/{id}          - Update group
DELETE /api/ktb-groups/{id}          - Delete group

// KtbRelationshipController
POST   /api/ktb-relationships        - Create mentoring relationship
PUT    /api/ktb-relationships/{id}   - Update relationship
DELETE /api/ktb-relationships/{id}   - Delete relationship
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter KtbMemberTest
```

## 📝 Notes

### Generation System
- **Generation 1**: Founder/Root members (tidak punya mentor)
- **Generation 2**: Direct mentees dari Generation 1
- **Generation 3**: Direct mentees dari Generation 2
- Dan seterusnya...

### Status Management
- **Member Status**:
  - `active`: Anggota aktif
  - `inactive`: Tidak aktif sementara
  - `alumni`: Sudah lulus/selesai

- **Group Status**:
  - `active`: Kelompok aktif
  - `inactive`: Tidak aktif sementara
  - `completed`: Kelompok sudah selesai

- **Relationship Status**:
  - `active`: Relasi mentoring aktif
  - `inactive`: Tidak aktif sementara
  - `graduated`: Mentee sudah lulus

### Soft Deletes
Semua tabel menggunakan soft deletes untuk menjaga history data. Data yang dihapus tidak benar-benar dihapus dari database, hanya ditandai dengan `deleted_at`.

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

[Specify your license here]

## 👥 Contact

[Your contact information]

---

**Created with ❤️ for KTB Community**
