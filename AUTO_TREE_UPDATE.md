# Auto Tree Update Feature

## Overview
Fitur ini secara otomatis memperbarui pohon KTB ketika anggota baru ditambahkan atau di-assign ke kelompok.

## Cara Kerja

### 1. Ketika Menambahkan Anggota Baru (Create Member)
Ketika anggota baru dibuat dan di-assign ke kelompok:
- System akan mencari **leader kelompok** tersebut
- Jika ada leader, akan otomatis membuat relasi mentor-mentee
- Mentor: Leader kelompok
- Mentee: Anggota baru
- Status: Rutin
- Notes: "Auto-assigned when joining group"

### 2. Ketika Memindahkan Anggota ke Kelompok Lain (Update Member)
Ketika anggota dipindahkan ke kelompok baru:
- System akan mendeteksi perubahan `current_group_id`
- Jika kelompok berubah, akan otomatis create relasi dengan leader kelompok baru
- Relasi lama tidak dihapus (untuk history)

### 3. Ketika Assign Members di Kelompok (Bulk Assignment)
Ketika melakukan bulk assignment anggota ke kelompok:
- Setiap anggota yang di-assign akan otomatis mendapat mentor
- Mentor: Leader kelompok atau senior member

## Logika Pemilihan Mentor

### Prioritas 1: Leader Kelompok
System akan mencari member dengan:
- `current_group_id` sama dengan kelompok target
- `is_leader = true`
- `id` berbeda dengan anggota yang akan di-assign

### Prioritas 2: Senior Member (jika tidak ada leader)
System akan mencari member dengan kriteria:
1. Generasi lebih rendah (senior)
2. Jika generasi sama, yang join lebih dulu (ID lebih kecil)
3. Diurutkan: generation ASC, id ASC

## Automatic Relationship Creation

Relasi yang dibuat otomatis memiliki:
```php
[
    'mentor_id' => $leader->id,
    'mentee_id' => $member->id,
    'group_id' => $member->current_group_id,
    'status' => 'rutin',
    'started_at' => now(),
    'notes' => 'Auto-assigned when joining group',
]
```

## Validasi

System melakukan validasi:
- ✅ Tidak membuat relasi duplikat (cek existing relationship)
- ✅ Tidak assign diri sendiri sebagai mentor
- ✅ Hanya berjalan untuk non-leader

## Contoh Skenario

### Skenario 1: Menambah Anggota Baru ke Kelompok Andi
1. User create member baru: "Budi", Generation 2
2. Assign ke kelompok "Kelompok Andi"
3. System detect ada leader: "Andi"
4. **Auto-create relationship**: Andi (mentor) → Budi (mentee)
5. Pohon KTB otomatis update: Budi muncul sebagai child dari Andi

### Skenario 2: Bulk Assignment di Kelompok
1. User masuk ke "Kelompok Andi" → Assign Members
2. Pilih 5 anggota baru
3. Klik Update
4. System loop 5 anggota:
   - Anggota 1: create relationship dengan Andi
   - Anggota 2: create relationship dengan Andi
   - ... dst
5. Semua 5 anggota muncul di pohon sebagai mentees Andi

### Skenario 3: Tidak Ada Leader
1. Kelompok tanpa leader
2. Ada member Gen 1: "Senior A" (id: 10)
3. Ada member Gen 2: "Senior B" (id: 15)
4. Tambah member baru Gen 3: "Junior C"
5. System pilih "Senior A" (generasi terendah)
6. Auto-create: Senior A → Junior C

## Files Modified

### KtbMemberController.php
- `store()`: Added auto-assign logic after member creation
- `update()`: Added auto-assign logic when group changes
- `autoAssignMentor()`: Private method for mentor assignment logic

### KtbGroupController.php
- `updateMembers()`: Added auto-assign for each member in bulk assignment
- `autoAssignMentor()`: Private method (duplicate logic)

## Testing

### Test Case 1: Create Member with Group
```
POST /ktb-members
Body: {
    name: "Test User",
    generation: 2,
    current_group_id: 1
}
Expected: 
- Member created
- Relationship created with group leader
- Visible in KTB tree
```

### Test Case 2: Assign Member to Group
```
PUT /ktb-groups/1/update-members
Body: {
    member_ids: [5, 6, 7]
}
Expected:
- 3 members assigned to group
- 3 relationships created
- All visible in tree under group leader
```

### Test Case 3: Update Member Group
```
PUT /ktb-members/5
Body: {
    current_group_id: 2  // changed from 1 to 2
}
Expected:
- Member moved to new group
- New relationship created with new group leader
- Old relationship still exists (history)
```

## Benefits

✅ **Otomatis**: Tidak perlu manual create relationship
✅ **Konsisten**: Pohon KTB selalu update dengan struktur kelompok
✅ **Flexible**: Bisa override dengan manual edit relationship
✅ **History**: Relasi lama tetap ada untuk tracking

## Notes

- Fitur ini tidak menghapus relasi lama (untuk maintain history)
- User masih bisa manual edit/delete relationship di member detail page
- Jika tidak ada leader atau senior member, tidak akan create relationship
- Leader tidak mendapat auto-assigned mentor (skip jika is_leader = true)
