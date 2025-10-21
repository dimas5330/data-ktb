# ⚡ Quick Reference - KTB System

## 📋 Cheat Sheet

### Model Methods

#### KtbMember
```php
// Relasi
$member->mentees            // Semua adik KTB
$member->mentors            // Semua kakak KTB
$member->currentGroup       // Kelompok saat ini
$member->leadingGroups      // Kelompok yang dipimpin

// Methods
$member->activeMentees()            // Adik KTB aktif
$member->hasOpenedNewGroup()        // Boolean: sudah multiplikasi?
$member->getAllDescendants()        // Collection: semua keturunan
$member->getTreeStructure()         // Array: struktur hierarki

// Attributes
$member->total_mentees_count        // Int: total adik
$member->active_mentees_count       // Int: adik aktif
```

#### KtbGroup
```php
// Relasi
$group->leader              // Pemimpin kelompok
$group->members             // Semua anggota
$group->relationships       // Relasi mentoring

// Methods
$group->isActive()          // Boolean: aktif?

// Attributes
$group->members_count       // Int: total anggota
```

---

## 🔍 Common Queries

### Get Member's Direct Mentees
```php
$member = KtbMember::find(1);
$mentees = $member->mentees;
```

### Get All Descendants (Recursive)
```php
$descendants = $member->getAllDescendants();
```

### Check if Member Has Multiplied
```php
if ($member->hasOpenedNewGroup()) {
    // Yes, sudah buka kelompok baru
}
```

### Get Group Statistics
```php
$group = KtbGroup::with('members')->find(1);
$total = $group->members()->count();
$leaders = $group->members()->where('is_leader', true)->count();
$rate = ($leaders / $total) * 100;
```

### Get Members by Generation
```php
$gen1 = KtbMember::where('generation', 1)->get();
$gen2 = KtbMember::where('generation', 2)->get();
```

### Get Active Groups
```php
$activeGroups = KtbGroup::where('status', 'active')
    ->with('leader', 'members')
    ->get();
```

### Create New Member
```php
KtbMember::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '08123456789',
    'current_group_id' => 1,
    'generation' => 2,
    'status' => 'active',
]);
```

### Create Mentoring Relationship
```php
KtbMemberRelationship::create([
    'mentor_id' => 1,      // Kakak KTB
    'mentee_id' => 2,      // Adik KTB
    'group_id' => 1,
    'started_at' => now(),
    'status' => 'active',
]);

// Or using relationship
$mentor->mentees()->attach($mentee->id, [
    'group_id' => 1,
    'started_at' => now(),
    'status' => 'active',
]);
```

### Create New Group (Multiplication)
```php
$member->update(['is_leader' => true]);

$group = KtbGroup::create([
    'name' => 'Kelompok Baru',
    'leader_id' => $member->id,
    'location' => 'Jakarta',
    'meeting_day' => 'Jumat',
    'meeting_time' => '19:00',
    'status' => 'active',
    'started_at' => now(),
]);
```

---

## 📊 Eloquent Relationships Map

```
User
 └─ hasOne → KtbMember
             ├─ belongsTo → KtbGroup (current_group)
             ├─ hasMany → KtbGroup (leading_groups)
             ├─ belongsToMany → KtbMember (mentees) via relationships
             └─ belongsToMany → KtbMember (mentors) via relationships

KtbGroup
 ├─ belongsTo → KtbMember (leader)
 ├─ hasMany → KtbMember (members)
 └─ hasMany → KtbMemberRelationship

KtbMemberRelationship
 ├─ belongsTo → KtbMember (mentor)
 ├─ belongsTo → KtbMember (mentee)
 └─ belongsTo → KtbGroup
```

---

## 🎯 Common Scenarios

### Scenario 1: Show Member Profile with Stats
```php
$member = KtbMember::with('currentGroup', 'mentees', 'leadingGroups')
    ->find($id);

return [
    'name' => $member->name,
    'group' => $member->currentGroup->name,
    'is_leader' => $member->is_leader,
    'direct_mentees' => $member->mentees->count(),
    'total_descendants' => $member->getAllDescendants()->count(),
    'groups_leading' => $member->leadingGroups->pluck('name'),
    'has_multiplied' => $member->hasOpenedNewGroup(),
];
```

### Scenario 2: List All Groups with Members
```php
$groups = KtbGroup::with(['leader', 'members'])
    ->where('status', 'active')
    ->get()
    ->map(function ($group) {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'leader' => $group->leader->name,
            'total_members' => $group->members->count(),
            'location' => $group->location,
            'meeting' => $group->meeting_day . ' ' . $group->meeting_time,
        ];
    });
```

### Scenario 3: Track Multiplication Rate
```php
$group = KtbGroup::with('members.leadingGroups')->find($id);
$totalMembers = $group->members->count();
$multipliers = $group->members->filter(fn($m) => $m->leadingGroups->isNotEmpty());
$rate = ($multipliers->count() / $totalMembers) * 100;

return [
    'total_members' => $totalMembers,
    'multipliers' => $multipliers->count(),
    'multiplication_rate' => round($rate, 2) . '%',
];
```

### Scenario 4: Generate Tree JSON
```php
$root = KtbMember::where('generation', 1)->first();
$tree = $root->getTreeStructure();
return response()->json($tree);
```

### Scenario 5: Get Generation Statistics
```php
$stats = KtbMember::selectRaw('generation, COUNT(*) as count')
    ->groupBy('generation')
    ->orderBy('generation')
    ->get()
    ->mapWithKeys(fn($s) => ["Gen-{$s->generation}" => $s->count]);
```

---

## 🗃️ Database Schema Quick Reference

### ktb_members
```sql
id, user_id, name, email, phone, address, birth_date, gender,
current_group_id, is_leader, joined_at, generation, status, notes,
created_at, updated_at, deleted_at
```

### ktb_groups
```sql
id, name, leader_id, description, location, meeting_day, meeting_time,
status, started_at, ended_at, created_at, updated_at, deleted_at
```

### ktb_member_relationships
```sql
id, mentor_id, mentee_id, group_id, started_at, ended_at,
status, notes, created_at, updated_at
```

---

## 🔐 Status Values

### Member Status
- `active` - Aktif
- `inactive` - Tidak aktif sementara
- `alumni` - Sudah lulus

### Group Status
- `active` - Aktif
- `inactive` - Tidak aktif sementara
- `completed` - Selesai

### Relationship Status
- `active` - Aktif
- `inactive` - Tidak aktif sementara
- `graduated` - Sudah lulus

---

## 🚀 CLI Commands

```bash
# Migration
php artisan migrate
php artisan migrate:fresh      # Drop all & recreate
php artisan migrate:rollback   # Rollback last batch

# Seeding
php artisan db:seed --class=KtbSeeder

# Testing
php artisan tinker test_ktb_data.php

# Generate Models/Controllers
php artisan make:model ModelName
php artisan make:controller ControllerName
php artisan make:migration create_table_name
```

---

## 📚 File Locations

```
Models:           app/Models/Ktb*.php
Migrations:       database/migrations/*ktb*.php
Seeders:          database/seeders/KtbSeeder.php
Documentation:    docs/*.md
Test Script:      test_ktb_data.php
```

---

## 💡 Tips & Best Practices

1. **Always use eager loading** untuk avoid N+1 queries
   ```php
   KtbMember::with('mentees', 'currentGroup')->get();
   ```

2. **Use scopes** untuk query yang sering dipakai
   ```php
   // In KtbMember model
   public function scopeActive($query) {
       return $query->where('status', 'active');
   }
   // Usage: KtbMember::active()->get();
   ```

3. **Cache expensive queries** seperti getAllDescendants()
   ```php
   Cache::remember("member.{$id}.descendants", 3600, function () use ($member) {
       return $member->getAllDescendants();
   });
   ```

4. **Use soft deletes** untuk maintain data integrity
   ```php
   $member->delete();              // Soft delete
   $member->forceDelete();         // Permanent delete
   $member->restore();             // Restore soft deleted
   ```

5. **Validate before creating relationships**
   ```php
   // Check if relationship already exists
   if (!$mentor->mentees()->where('id', $mentee->id)->exists()) {
       $mentor->mentees()->attach($mentee->id);
   }
   ```

---

## 🐛 Common Issues & Solutions

### Issue: Circular dependency in foreign keys
**Solution:** Create tables first, add foreign keys later (done in migration)

### Issue: N+1 query problem
**Solution:** Use eager loading
```php
// Bad
$members = KtbMember::all();
foreach ($members as $member) {
    echo $member->currentGroup->name; // N+1 query!
}

// Good
$members = KtbMember::with('currentGroup')->all();
foreach ($members as $member) {
    echo $member->currentGroup->name; // Single query
}
```

### Issue: Memory limit on large recursive queries
**Solution:** Use pagination or chunk
```php
KtbMember::chunk(100, function ($members) {
    foreach ($members as $member) {
        // Process
    }
});
```

---

## 📞 Quick Links

- [Full Documentation](INDEX.md)
- [Database Design](DATABASE_DESIGN.md)
- [Visual Guide](VISUAL_GUIDE.md)
- [Summary](SUMMARY.md)

---

**Last Updated:** October 21, 2025
