# Desain Database KTB (Kelompok Tumbuh Bersama)

## Overview
Sistem ini dirancang untuk mendata pohon hierarki KTB dengan struktur multiplikasi, dimana setiap anggota dapat membimbing anggota lain (adik KTB) dan membuka kelompok KTB baru.

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│     users       │
└────────┬────────┘
         │ 1
         │
         │ 0..1
┌────────▼────────────────┐         ┌──────────────────────┐
│    ktb_members          │ 1     * │    ktb_groups        │
│  ───────────────        │◄────────┤  ─────────────       │
│  - id                   │ current │  - id                │
│  - user_id              │  group  │  - name              │
│  - name                 │         │  - leader_id         │
│  - email                │         │  - description       │
│  - phone                │    *    │  - location          │
│  - current_group_id     ├─────────►  - meeting_day       │
│  - is_leader            │ leader  │  - meeting_time      │
│  - generation           │    1    │  - status            │
│  - status               │         │  - started_at        │
└────┬────────────────┬───┘         └──────────────────────┘
     │                │
     │ mentor         │ mentee
     │ (Kakak KTB)    │ (Adik KTB)
     │ *              │ *
     │    ┌───────────▼──────────────────┐
     │    │ ktb_member_relationships     │
     │    │  ────────────────────────    │
     │    │  - id                        │
     └────►  - mentor_id (Kakak KTB)    │
          │  - mentee_id (Adik KTB)     │
          │  - group_id                 │
          │  - started_at               │
          │  - ended_at                 │
          │  - status                   │
          └─────────────────────────────┘
```

## Tabel dan Relasi

### 1. Tabel `ktb_members`
**Deskripsi:** Menyimpan data anggota KTB

**Kolom:**
- `id`: Primary key
- `user_id`: Foreign key ke tabel users (nullable)
- `name`: Nama anggota
- `email`: Email anggota
- `phone`: Nomor telepon
- `address`: Alamat
- `birth_date`: Tanggal lahir
- `gender`: Jenis kelamin (male/female)
- `current_group_id`: Foreign key ke kelompok KTB saat ini
- `is_leader`: Boolean, apakah pemimpin kelompok
- `joined_at`: Tanggal bergabung
- `generation`: Generasi ke berapa (1 = founder, 2, 3, dst)
- `status`: Status anggota (active/inactive/alumni)
- `notes`: Catatan tambahan
- `timestamps`, `soft_deletes`

**Relasi:**
- `belongsTo User` - Link ke user sistem (optional)
- `belongsTo KtbGroup (current_group_id)` - Kelompok saat ini
- `hasMany KtbGroup (leader_id)` - Kelompok yang dipimpin
- `belongsToMany KtbMember (mentees)` - Adik-adik KTB yang dibimbing
- `belongsToMany KtbMember (mentors)` - Kakak-kakak KTB pembimbing

### 2. Tabel `ktb_groups`
**Deskripsi:** Menyimpan data kelompok KTB

**Kolom:**
- `id`: Primary key
- `name`: Nama kelompok
- `leader_id`: Foreign key ke ktb_members (pemimpin kelompok)
- `description`: Deskripsi kelompok
- `location`: Lokasi pertemuan
- `meeting_day`: Hari pertemuan (Senin, Selasa, dll)
- `meeting_time`: Jam pertemuan
- `status`: Status kelompok (active/inactive/completed)
- `started_at`: Tanggal mulai kelompok
- `ended_at`: Tanggal selesai kelompok
- `timestamps`, `soft_deletes`

**Relasi:**
- `belongsTo KtbMember (leader_id)` - Pemimpin kelompok
- `hasMany KtbMember (current_group_id)` - Anggota kelompok
- `hasMany KtbMemberRelationship` - Relasi mentoring dalam kelompok

### 3. Tabel `ktb_member_relationships`
**Deskripsi:** Menyimpan relasi hierarki antara anggota (Kakak KTB - Adik KTB)

**Kolom:**
- `id`: Primary key
- `mentor_id`: Foreign key ke ktb_members (Kakak KTB)
- `mentee_id`: Foreign key ke ktb_members (Adik KTB)
- `group_id`: Foreign key ke ktb_groups (Kelompok tempat relasi ini)
- `started_at`: Tanggal mulai mentoring
- `ended_at`: Tanggal selesai mentoring
- `status`: Status relasi (active/inactive/graduated)
- `notes`: Catatan relasi
- `timestamps`
- `unique(mentor_id, mentee_id, group_id)` - Prevent duplicate relationships

**Relasi:**
- `belongsTo KtbMember (mentor_id)` - Kakak KTB
- `belongsTo KtbMember (mentee_id)` - Adik KTB
- `belongsTo KtbGroup` - Kelompok

## Contoh Kasus: Andi dan Struktur KTB-nya

### Skenario:
1. **Andi** memiliki kelompok KTB dengan 4 adik KTB: A, B, C, D
2. **A** membuka kelompok baru dengan 3 adik KTB: E, F, G
3. **B** juga membuka kelompok baru dengan 2 adik KTB: H, I

### Data di Database:

#### Tabel `ktb_members`:
```
id | name | current_group_id | is_leader | generation
1  | Andi | 1                | true      | 1
2  | A    | 1                | false     | 2
3  | B    | 1                | false     | 2
4  | C    | 1                | false     | 2
5  | D    | 1                | false     | 2
6  | E    | 2                | false     | 3
7  | F    | 2                | false     | 3
8  | G    | 2                | false     | 3
9  | H    | 3                | false     | 3
10 | I    | 3                | false     | 3
```

#### Tabel `ktb_groups`:
```
id | name              | leader_id | status
1  | Kelompok Andi     | 1         | active
2  | Kelompok A        | 2         | active
3  | Kelompok B        | 3         | active
```

#### Tabel `ktb_member_relationships`:
```
id | mentor_id | mentee_id | group_id | status
1  | 1 (Andi)  | 2 (A)     | 1        | active
2  | 1 (Andi)  | 3 (B)     | 1        | active
3  | 1 (Andi)  | 4 (C)     | 1        | active
4  | 1 (Andi)  | 5 (D)     | 1        | active
5  | 2 (A)     | 6 (E)     | 2        | active
6  | 2 (A)     | 7 (F)     | 2        | active
7  | 2 (A)     | 8 (G)     | 2        | active
8  | 3 (B)     | 9 (H)     | 3        | active
9  | 3 (B)     | 10 (I)    | 3        | active
```

## Query Examples

### 1. Mendapatkan semua adik KTB langsung dari Andi:
```php
$andi = KtbMember::where('name', 'Andi')->first();
$adikKtb = $andi->mentees; // Return: A, B, C, D
```

### 2. Mendapatkan total adik KTB Andi (langsung):
```php
$totalAdikKtb = $andi->mentees()->count(); // Return: 4
```

### 3. Cek apakah adik KTB Andi ada yang membuka kelompok baru:
```php
foreach ($andi->mentees as $adik) {
    if ($adik->hasOpenedNewGroup()) {
        echo "{$adik->name} telah membuka kelompok baru\n";
        echo "Jumlah anggota: {$adik->mentees()->count()}\n";
    }
}
```

### 4. Mendapatkan struktur pohon lengkap dari Andi:
```php
$tree = $andi->getTreeStructure();
// Return struktur hierarki lengkap dengan semua keturunan
```

### 5. Mendapatkan semua keturunan (descendants) Andi secara rekursif:
```php
$allDescendants = $andi->getAllDescendants();
// Return: A, B, C, D, E, F, G, H, I (semua keturunan)
```

### 6. Statistik kelompok Andi:
```php
$kelompokAndi = KtbGroup::find(1);
$totalAnggota = $kelompokAndi->members_count;
$anggotaAktif = $kelompokAndi->members()->where('status', 'active')->count();

// Cek berapa anggota yang sudah membuka kelompok baru
$multipliers = $kelompokAndi->members()
    ->whereHas('leadingGroups')
    ->get();
```

### 7. Mendapatkan generasi-generasi dari Andi:
```php
// Generasi 1: Andi
$gen1 = KtbMember::where('generation', 1)->get();

// Generasi 2: A, B, C, D
$gen2 = KtbMember::where('generation', 2)->get();

// Generasi 3: E, F, G, H, I
$gen3 = KtbMember::where('generation', 3)->get();
```

## Fitur Utama Model

### KtbMember Model:
- `mentees()` - Mendapatkan semua adik KTB
- `mentors()` - Mendapatkan semua kakak KTB
- `activeMentees()` - Mendapatkan adik KTB yang aktif
- `leadingGroups()` - Kelompok yang dipimpin
- `hasOpenedNewGroup()` - Cek apakah sudah membuka kelompok baru
- `getAllDescendants()` - Mendapatkan semua keturunan secara rekursif
- `getTreeStructure()` - Mendapatkan struktur pohon hierarki

### KtbGroup Model:
- `leader()` - Pemimpin kelompok
- `members()` - Semua anggota kelompok
- `relationships()` - Semua relasi mentoring dalam kelompok
- `isActive()` - Cek status aktif kelompok

## Instalasi dan Migrasi

Jalankan migrasi untuk membuat tabel:
```bash
php artisan migrate
```

## Catatan Penting

1. **Generation Field**: Digunakan untuk tracking level hierarki
   - Generation 1: Founder/Root
   - Generation 2: Anak langsung dari founder
   - Generation 3: Cucu dari founder, dst

2. **Soft Deletes**: Semua tabel menggunakan soft deletes untuk histori data

3. **Status Management**: 
   - Member status: active, inactive, alumni
   - Group status: active, inactive, completed
   - Relationship status: active, inactive, graduated

4. **Unique Constraint**: Mencegah duplikasi relasi mentor-mentee dalam kelompok yang sama

5. **Recursive Queries**: Hati-hati dengan query rekursif pada data yang sangat besar, pertimbangkan pagination atau caching
