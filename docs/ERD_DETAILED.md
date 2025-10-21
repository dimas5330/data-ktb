# Entity Relationship Diagram (ERD) - Sistem KTB

## Diagram Lengkap

```
┌─────────────────────────────────┐
│           users                 │
│  ───────────────────────────    │
│  PK: id                         │
│      name                       │
│      email                      │
│      password                   │
│      ...                        │
└────────────────┬────────────────┘
                 │
                 │ 1:0..1
                 │
                 │
┌────────────────▼───────────────────────────────────────┐
│                    ktb_members                         │
│  ──────────────────────────────────────────────────    │
│  PK: id                                                │
│  FK: user_id (nullable) → users.id                     │
│  FK: current_group_id (nullable) → ktb_groups.id       │
│      name VARCHAR(255) NOT NULL                        │
│      email VARCHAR(255)                                │
│      phone VARCHAR(255)                                │
│      address TEXT                                      │
│      birth_date DATE                                   │
│      gender ENUM('male','female')                      │
│      is_leader BOOLEAN DEFAULT false                   │
│      joined_at DATE                                    │
│      generation INT DEFAULT 1                          │
│      status ENUM('active','inactive','alumni')         │
│      notes TEXT                                        │
│      created_at TIMESTAMP                              │
│      updated_at TIMESTAMP                              │
│      deleted_at TIMESTAMP (soft delete)                │
└──────────┬─────────────────────────────┬───────────────┘
           │                             │
           │ 1:*                         │ 1:*
           │ (leader)                    │ (members)
           │                             │
           │    ┌────────────────────────┘
           │    │
┌──────────▼────▼─────────────────────────────────────┐
│                ktb_groups                           │
│  ───────────────────────────────────────────────    │
│  PK: id                                             │
│  FK: leader_id (nullable) → ktb_members.id          │
│      name VARCHAR(255) NOT NULL                     │
│      description TEXT                               │
│      location VARCHAR(255)                          │
│      meeting_day VARCHAR(255)                       │
│      meeting_time TIME                              │
│      status ENUM('active','inactive','completed')   │
│      started_at DATE                                │
│      ended_at DATE                                  │
│      created_at TIMESTAMP                           │
│      updated_at TIMESTAMP                           │
│      deleted_at TIMESTAMP (soft delete)             │
└─────────────────────┬───────────────────────────────┘
                      │
                      │ 1:*
                      │
                      │
┌─────────────────────▼──────────────────────────────────┐
│         ktb_member_relationships (Pivot)               │
│  ──────────────────────────────────────────────────    │
│  PK: id                                                │
│  FK: mentor_id → ktb_members.id (Kakak KTB)            │
│  FK: mentee_id → ktb_members.id (Adik KTB)             │
│  FK: group_id (nullable) → ktb_groups.id               │
│      started_at DATE                                   │
│      ended_at DATE                                     │
│      status ENUM('active','inactive','graduated')      │
│      notes TEXT                                        │
│      created_at TIMESTAMP                              │
│      updated_at TIMESTAMP                              │
│  UNIQUE: (mentor_id, mentee_id, group_id)              │
│  INDEX: mentor_id                                      │
│  INDEX: mentee_id                                      │
│  INDEX: group_id                                       │
└────────────────────────────────────────────────────────┘
           │                    │
           │ *                  │ *
           │                    │
           └────────┬───────────┘
                    │
       Self-Referencing (Recursive Relationship)
       mentor_id dan mentee_id sama-sama menunjuk ke ktb_members
```

## Penjelasan Relasi

### 1. users ↔ ktb_members (One-to-Zero-or-One)
- **users.id** → **ktb_members.user_id**
- Satu user bisa menjadi satu ktb_member (optional)
- Relasi ini memungkinkan integrasi dengan sistem autentikasi
- `nullable` karena bisa ada anggota KTB yang belum punya akun user

### 2. ktb_members ↔ ktb_groups (Many-to-One) - Current Group
- **ktb_members.current_group_id** → **ktb_groups.id**
- Satu member berada di satu kelompok
- Satu kelompok memiliki banyak members
- `nullable` karena bisa ada member yang belum masuk kelompok

### 3. ktb_members ↔ ktb_groups (One-to-Many) - Leader
- **ktb_groups.leader_id** → **ktb_members.id**
- Satu member bisa memimpin banyak kelompok (seiring waktu)
- Satu kelompok dipimpin oleh satu leader
- `nullable` karena kelompok bisa tidak punya leader sementara

### 4. ktb_members ↔ ktb_members (Many-to-Many) via ktb_member_relationships
- **Self-Referencing Relationship**
- **mentor_id** → ktb_members.id (Kakak KTB)
- **mentee_id** → ktb_members.id (Adik KTB)
- Satu member bisa menjadi mentor banyak mentee
- Satu member bisa punya banyak mentor (secara teoritis, tapi biasanya 1)
- Membentuk struktur hierarki pohon

### 5. ktb_groups ↔ ktb_member_relationships (One-to-Many)
- **ktb_member_relationships.group_id** → **ktb_groups.id**
- Setiap relasi mentoring terjadi dalam konteks kelompok tertentu
- Satu kelompok bisa punya banyak relasi mentoring
- `nullable` untuk fleksibilitas (mentoring di luar kelompok)

## Kardinalitas

```
users (1) ──────────── (0..1) ktb_members

ktb_members (1) ─┬───── (*) ktb_member_relationships (mentor_id)
                 │
                 └───── (*) ktb_member_relationships (mentee_id)

ktb_groups (1) ──┬───── (*) ktb_members (current_group_id)
                 │
                 ├───── (0..1) ktb_members (leader_id) [reverse]
                 │
                 └───── (*) ktb_member_relationships (group_id)
```

## Constraint dan Index

### Foreign Keys:
```sql
-- ktb_members
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
FOREIGN KEY (current_group_id) REFERENCES ktb_groups(id) ON DELETE SET NULL

-- ktb_groups
FOREIGN KEY (leader_id) REFERENCES ktb_members(id) ON DELETE SET NULL

-- ktb_member_relationships
FOREIGN KEY (mentor_id) REFERENCES ktb_members(id) ON DELETE CASCADE
FOREIGN KEY (mentee_id) REFERENCES ktb_members(id) ON DELETE CASCADE
FOREIGN KEY (group_id) REFERENCES ktb_groups(id) ON DELETE SET NULL
```

### Unique Constraints:
```sql
-- Mencegah duplikasi relasi mentor-mentee dalam kelompok yang sama
UNIQUE KEY unique_mentor_mentee_group (mentor_id, mentee_id, group_id)
```

### Indexes:
```sql
-- ktb_member_relationships
INDEX idx_mentor_id (mentor_id)
INDEX idx_mentee_id (mentee_id)
INDEX idx_group_id (group_id)

-- ktb_members
INDEX idx_current_group_id (current_group_id)
INDEX idx_generation (generation)
INDEX idx_status (status)

-- ktb_groups
INDEX idx_leader_id (leader_id)
INDEX idx_status (status)
```

## Normalisasi

Database ini mengikuti **Third Normal Form (3NF)**:

### 1NF (First Normal Form):
✅ Setiap kolom berisi nilai atomic (tidak ada array atau nested data)
✅ Setiap baris unik dengan primary key

### 2NF (Second Normal Form):
✅ Memenuhi 1NF
✅ Tidak ada partial dependency (semua non-key attributes bergantung pada seluruh primary key)

### 3NF (Third Normal Form):
✅ Memenuhi 2NF
✅ Tidak ada transitive dependency (non-key attributes tidak bergantung pada non-key attributes lain)

## Scalability Considerations

### Untuk Data Besar:
1. **Partitioning**: Bisa partition `ktb_member_relationships` berdasarkan `group_id` atau tahun
2. **Archiving**: Soft-deleted records bisa dipindah ke archive table
3. **Caching**: Cache hasil query recursive untuk performa
4. **Materialized Views**: Buat view untuk statistik yang sering diakses

### Query Optimization:
```sql
-- Untuk mendapatkan tree structure, gunakan recursive CTE
WITH RECURSIVE ktb_tree AS (
    -- Base case: root members
    SELECT id, name, generation, 1 as level
    FROM ktb_members
    WHERE generation = 1
    
    UNION ALL
    
    -- Recursive case: children
    SELECT m.id, m.name, m.generation, t.level + 1
    FROM ktb_members m
    INNER JOIN ktb_member_relationships r ON m.id = r.mentee_id
    INNER JOIN ktb_tree t ON r.mentor_id = t.id
)
SELECT * FROM ktb_tree;
```
