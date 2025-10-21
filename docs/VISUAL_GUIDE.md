# 🎨 Visual Guide - Database KTB

## 🌳 Konsep Dasar: Pohon KTB

Sistem KTB (Kelompok Tumbuh Bersama) bekerja seperti pohon keluarga:

```
         👤 ANDI (Founder)
         └─ Kelompok Andi
              │
    ┌─────────┼─────────┬──────┐
    │         │         │      │
   👤A       👤B       👤C    👤D
    │         │
 [Leader]  [Leader]
    │         │
Kelompok A  Kelompok B
    │         │
┌───┼───┐   ┌─┴─┐
│   │   │   │   │
E   F   G   H   I
```

**Istilah:**
- **Kakak KTB** = Mentor/Pembimbing (mis: Andi adalah kakak KTB dari A, B, C, D)
- **Adik KTB** = Mentee/Yang dibimbing (mis: A, B, C, D adalah adik KTB dari Andi)
- **Multiplikasi** = Saat seorang adik KTB membuka kelompok baru dan menjadi leader
- **Generation** = Level dalam hierarki (1=Root, 2=Children, 3=Grandchildren, ...)

---

## 📊 3 Tabel Utama

### 1️⃣ Tabel `ktb_members` (Anggota)

```
┌─────────────────────────────────┐
│      ktb_members Table          │
├─────────────────────────────────┤
│ id: 1                           │
│ name: "Andi"                    │
│ current_group_id: 1 ─┐          │
│ is_leader: true       │          │
│ generation: 1         │          │
│ status: active        │          │
└───────────────────────┼──────────┘
                        │
                        │ Belongs to
                        ▼
          ┌─────────────────────────┐
          │   ktb_groups Table      │
          ├─────────────────────────┤
          │ id: 1                   │
          │ name: "Kelompok Andi"   │
          │ leader_id: 1            │
          │ status: active          │
          └─────────────────────────┘
```

---

### 2️⃣ Tabel `ktb_groups` (Kelompok)

```
┌─────────────────────────────────┐
│     ktb_groups Table            │
├─────────────────────────────────┤
│ id: 1                           │
│ name: "Kelompok Andi"           │
│ leader_id: 1 ───────┐           │
│ location: "Jakarta"  │           │
│ meeting_day: "Jumat" │           │
│ meeting_time: "19:00"│           │
│ status: active       │           │
└──────────────────────┼───────────┘
                       │
          Has Many    │
          Members     │
                       ▼
         ┌─────────────────────┐
         │   ktb_members       │
         │ - Andi (leader)     │
         │ - A                 │
         │ - B                 │
         │ - C                 │
         │ - D                 │
         └─────────────────────┘
```

---

### 3️⃣ Tabel `ktb_member_relationships` (Relasi)

```
┌────────────────────────────────────────┐
│  ktb_member_relationships Table        │
├────────────────────────────────────────┤
│ id: 1                                  │
│ mentor_id: 1 (Andi) ──┐                │
│ mentee_id: 2 (A)      │                │
│ group_id: 1           │                │
│ status: active        │                │
└───────────────────────┼────────────────┘
                        │
            Connects    │
                        ▼
          👤 Andi ──mentoring──► 👤 A
          (Kakak)                (Adik)
```

**Satu baris di tabel ini = Satu relasi mentoring**

Contoh data:
```
mentor_id | mentee_id | group_id | Artinya
----------|-----------|----------|---------------------------
    1     |     2     |    1     | Andi membimbing A di Kelompok Andi
    1     |     3     |    1     | Andi membimbing B di Kelompok Andi
    2     |     6     |    2     | A membimbing E di Kelompok A
```

---

## 🔄 Alur Multiplikasi

### Step 1: Kelompok Awal
```
┌─────────────────┐
│ Kelompok Andi   │
│ Leader: Andi    │
│ Members: 4      │
│  - A            │
│  - B            │
│  - C            │
│  - D            │
└─────────────────┘
```

### Step 2: A Membuka Kelompok Baru (Multiplikasi)
```
┌─────────────────┐          ┌─────────────────┐
│ Kelompok Andi   │          │ Kelompok A      │
│ Leader: Andi    │◄─────────│ Leader: A       │
│ Members: 4      │ Parent   │ Members: 3      │
│  - A ✓          │          │  - E            │
│  - B            │          │  - F            │
│  - C            │          │  - G            │
│  - D            │          └─────────────────┘
└─────────────────┘
```

### Step 3: B Juga Multiplikasi
```
┌─────────────────┐
│ Kelompok Andi   │
│ Leader: Andi    │
│ Members: 4      │
│  - A ✓──────────┼───► Kelompok A (3 members)
│  - B ✓──────────┼───► Kelompok B (2 members)
│  - C            │
│  - D            │
└─────────────────┘
```

**Result:** 
- 1 kelompok → 3 kelompok
- 4 anggota → 10 anggota total
- Multiplikasi rate: 50% (2 dari 4 sudah buka kelompok)

---

## 🎯 Query Visual Examples

### 1. Mendapatkan Adik KTB

**Query:**
```php
$andi = KtbMember::find(1);
$adikKtb = $andi->mentees;
```

**Visual Result:**
```
        Andi
         │
    ┌────┼────┬────┐
    │    │    │    │
    A    B    C    D
    ↑    ↑    ↑    ↑
  adik adik adik adik
```

---

### 2. Cek Multiplikasi

**Query:**
```php
foreach ($andi->mentees as $adik) {
    if ($adik->hasOpenedNewGroup()) {
        echo "{$adik->name} sudah multiplikasi!";
    }
}
```

**Visual Result:**
```
        Andi
         │
    ┌────┼────┬────┐
    │    │    │    │
    A✓   B✓   C    D
    │    │
 (Leader of     (Leader of
  Kelompok A)    Kelompok B)
```

---

### 3. Mendapatkan Semua Keturunan

**Query:**
```php
$descendants = $andi->getAllDescendants();
// Returns: A, B, C, D, E, F, G, H, I
```

**Visual Result:**
```
                Andi
                 │
        ┌────────┼────────┬────┬────┐
        │        │        │    │    │
        A        B        C    D    │
        │        │                  │
    ┌───┼───┐   ┌┴─┐               │
    │   │   │   │  │               │
    E   F   G   H  I               │
    └───────────────────────────────┘
         All Descendants (9)
```

---

### 4. Struktur Pohon Lengkap

**Query:**
```php
$tree = $andi->getTreeStructure();
```

**Visual Result (JSON):**
```json
{
  "id": 1,
  "name": "Andi",
  "is_leader": true,
  "current_group": "Kelompok Andi",
  "leading_groups": ["Kelompok Andi"],
  "total_direct_mentees": 4,
  "mentees": [
    {
      "id": 2,
      "name": "A",
      "is_leader": true,
      "leading_groups": ["Kelompok A"],
      "total_direct_mentees": 3,
      "mentees": [...]
    },
    {
      "id": 3,
      "name": "B",
      "is_leader": true,
      "leading_groups": ["Kelompok B"],
      "total_direct_mentees": 2,
      "mentees": [...]
    },
    ...
  ]
}
```

---

## 📈 Dashboard Metrics Visual

### Statistik Andi:
```
┌─────────────────────────────────────────┐
│         PROFIL ANDI                     │
├─────────────────────────────────────────┤
│ 👤 Nama: Andi                           │
│ 🏢 Kelompok: Kelompok Andi              │
│ 🎯 Role: Leader                         │
│ 📊 Generation: 1 (Founder)              │
├─────────────────────────────────────────┤
│ STATISTIK MENTORING:                    │
│   👥 Adik KTB Langsung: 4 orang         │
│   ✓  Yang Multiplikasi: 2 orang (50%)   │
│   🌳 Total Keturunan: 9 orang           │
│   📈 Kelompok Turunan: 2 kelompok       │
├─────────────────────────────────────────┤
│ DAFTAR ADIK KTB:                        │
│   1. A ✓ (Leader - 3 adik)              │
│   2. B ✓ (Leader - 2 adik)              │
│   3. C                                  │
│   4. D                                  │
└─────────────────────────────────────────┘
```

---

## 🎨 Status & Colors (UI Suggestion)

### Member Status:
```
🟢 Active   - Anggota aktif
🟡 Inactive - Tidak aktif sementara
🔵 Alumni   - Sudah lulus/selesai
```

### Group Status:
```
🟢 Active    - Kelompok aktif
🟡 Inactive  - Tidak aktif sementara
⚫ Completed - Kelompok sudah selesai
```

### Relationship Status:
```
🟢 Active     - Relasi mentoring aktif
🟡 Inactive   - Tidak aktif sementara
🎓 Graduated  - Mentee sudah lulus
```

### Leadership Badge:
```
👑 Leader         - Pemimpin kelompok
⭐ Multiplier     - Sudah buka kelompok baru
🌱 Member         - Anggota biasa
🌳 Senior Leader  - Leader dengan banyak keturunan
```

---

## 🔍 Filter & Search Examples

### Filter by Generation:
```
Gen 1 (Founder)    🔵 Andi
Gen 2 (Children)   🟢 A, B, C, D
Gen 3 (Grandch.)   🟡 E, F, G, H, I
```

### Filter by Status:
```
Leaders    👑 Andi, A, B
Members    👥 C, D, E, F, G, H, I
```

### Filter by Multiplication:
```
Multipliers      ⭐ A (3 adik), B (2 adik)
Not Yet         🌱 C, D, E, F, G, H, I
```

---

## 📊 Reports Visual

### Laporan Pertumbuhan:
```
┌─────────────────────────────────────────┐
│     LAPORAN PERTUMBUHAN KTB             │
├─────────────────────────────────────────┤
│ Periode: 2020 - 2025                    │
│                                         │
│ 2020 │ ████████ 1 kelompok (4 org)     │
│ 2021 │ ████████████████ 3 kelompok     │
│      │ (10 orang)                      │
│                                         │
│ Growth Rate: +200% (kelompok)           │
│              +150% (anggota)            │
└─────────────────────────────────────────┘
```

### Multiplikasi Funnel:
```
┌────────────────────────────────────┐
│   MULTIPLICATION FUNNEL             │
├────────────────────────────────────┤
│ Gen 1: 1 founder                   │
│   └─► 4 adik (400%)                │
│                                    │
│ Gen 2: 4 members                   │
│   └─► 2 leaders (50%)              │
│   └─► 5 adik (125%)                │
│                                    │
│ Gen 3: 5 members                   │
│   └─► 0 leaders (0%) [Potential!]  │
└────────────────────────────────────┘
```

---

## 💡 UI Component Ideas

### 1. Tree View Component
```
📁 Kelompok Andi (4 members)
 ├─ 👑 Andi (Leader)
 ├─ ⭐ A → [Kelompok A] (3 members)
 ├─ ⭐ B → [Kelompok B] (2 members)
 ├─ 🌱 C
 └─ 🌱 D
```

### 2. Member Card
```
┌─────────────────────────┐
│    👤 ANDI              │
├─────────────────────────┤
│ 🏢 Kelompok Andi        │
│ 👑 Leader               │
│ 📊 Generation 1         │
│ 🟢 Active               │
├─────────────────────────┤
│ 👥 4 Adik KTB           │
│ ⭐ 2 Multiplikasi       │
│ 🌳 9 Keturunan          │
└─────────────────────────┘
```

### 3. Group Card
```
┌─────────────────────────┐
│  📁 KELOMPOK ANDI       │
├─────────────────────────┤
│ 👑 Leader: Andi         │
│ 👥 Members: 4           │
│ 📍 Jakarta Selatan      │
│ 📅 Jumat, 19:00         │
│ 🟢 Active               │
├─────────────────────────┤
│ ⭐ 2 Multiplikasi (50%) │
└─────────────────────────┘
```

---

**Created with ❤️ for better KTB management**
