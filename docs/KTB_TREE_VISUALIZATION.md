# Visualisasi Pohon KTB - Contoh Kasus Andi

## Struktur Hierarki

```
                        ┌──────────────────────────┐
                        │    ANDI (Generation 1)   │
                        │  Kelompok: Kelompok Andi │
                        │  Total Adik: 4 orang     │
                        └────────────┬─────────────┘
                                     │
                 ┌───────────────────┼───────────────────┐
                 │                   │                   │
        ┌────────▼────────┐  ┌──────▼──────┐  ┌────────▼────────┐  ┌────────────┐
        │   A (Gen 2)     │  │  B (Gen 2)  │  │   C (Gen 2)     │  │  D (Gen 2) │
        │ ✓ Leader        │  │ ✓ Leader    │  │                 │  │            │
        │ Kelompok: A     │  │ Kelompok: B │  │                 │  │            │
        │ Adik: 3 orang   │  │ Adik: 2 org │  │                 │  │            │
        └────────┬────────┘  └──────┬──────┘  └─────────────────┘  └────────────┘
                 │                  │
         ┌───────┼────────┐    ┌────┴─────┐
         │       │        │    │          │
    ┌────▼──┐ ┌─▼───┐ ┌──▼──┐ ┌▼─────┐ ┌─▼────┐
    │E(G3) │ │F(G3)│ │G(G3)│ │H(G3) │ │I(G3) │
    └──────┘ └─────┘ └─────┘ └──────┘ └──────┘
```

## Legenda:
- **Gen 1**: Generation 1 (Founder/Root)
- **Gen 2**: Generation 2 (Anak langsung dari founder)
- **Gen 3**: Generation 3 (Cucu dari founder)
- **✓ Leader**: Telah membuka kelompok KTB baru (multiplikasi)

## Detail Kelompok

### 1. Kelompok Andi (Generation 1)
- **Pemimpin**: Andi
- **Anggota**: 4 orang (A, B, C, D)
- **Lokasi**: Jakarta Selatan
- **Hari Pertemuan**: Jumat, 19:00
- **Status**: Active
- **Multiplikasi**: 2 kelompok (A dan B membuka kelompok baru)

### 2. Kelompok A (Generation 2 - Multiplikasi dari Andi)
- **Pemimpin**: A
- **Anggota**: 3 orang (E, F, G)
- **Lokasi**: Jakarta Timur
- **Hari Pertemuan**: Kamis, 19:30
- **Status**: Active
- **Parent**: Kelompok Andi

### 3. Kelompok B (Generation 2 - Multiplikasi dari Andi)
- **Pemimpin**: B
- **Anggota**: 2 orang (H, I)
- **Lokasi**: Jakarta Barat
- **Hari Pertemuan**: Rabu, 18:00
- **Status**: Active
- **Parent**: Kelompok Andi

## Statistik

### Total Keseluruhan
- **Total Kelompok**: 3 kelompok
- **Total Anggota**: 10 orang
  - Generation 1: 1 orang (Andi)
  - Generation 2: 4 orang (A, B, C, D)
  - Generation 3: 5 orang (E, F, G, H, I)
- **Total Relasi Mentoring**: 9 relasi
- **Tingkat Multiplikasi**: 50% (2 dari 4 anggota Gen-2 telah membuka kelompok)

### Per Kelompok
1. **Kelompok Andi**: 4 anggota aktif, 2 telah bermultiplikasi (50%)
2. **Kelompok A**: 3 anggota aktif, 0 telah bermultiplikasi (belum)
3. **Kelompok B**: 2 anggota aktif, 0 telah bermultiplikasi (belum)

## Relasi Mentoring (Kakak-Adik KTB)

### Direct Mentoring dari Andi:
1. Andi → A (aktif sejak Feb 2020)
2. Andi → B (aktif sejak Mar 2020)
3. Andi → C (aktif sejak Apr 2020)
4. Andi → D (aktif sejak Mei 2020)

### Direct Mentoring dari A:
5. A → E (aktif sejak Feb 2021)
6. A → F (aktif sejak Mar 2021)
7. A → G (aktif sejak Apr 2021)

### Direct Mentoring dari B:
8. B → H (aktif sejak Jul 2021)
9. B → I (aktif sejak Agu 2021)

## Analisis Pertumbuhan

### Timeline Pertumbuhan:
- **2020 Q1**: Kelompok Andi terbentuk dengan 4 anggota
- **2021 Q1**: A membuka Kelompok A dengan 3 anggota
- **2021 Q2**: B membuka Kelompok B dengan 2 anggota

### Proyeksi:
Jika setiap anggota Generation 3 (E, F, G, H, I) juga membuka kelompok dengan rata-rata 3 anggota:
- Akan terbentuk 5 kelompok baru (Generation 4)
- Akan ada 15 anggota baru (Generation 4)
- Total keseluruhan: 8 kelompok, 25 anggota

## Query untuk Mendapatkan Data Ini

```php
// Mendapatkan struktur Andi
$andi = KtbMember::where('name', 'Andi')->first();

// Total adik langsung
$totalAdikLangsung = $andi->mentees()->count(); // 4

// Adik yang sudah multiplikasi
$adikMultiplikasi = $andi->mentees()
    ->whereHas('leadingGroups')
    ->get(); // A dan B

// Total semua keturunan (recursive)
$semuaKeturunan = $andi->getAllDescendants(); // A, B, C, D, E, F, G, H, I (9 orang)

// Struktur pohon lengkap
$pohonKTB = $andi->getTreeStructure();
```
