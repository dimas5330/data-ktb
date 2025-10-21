<?php

use App\Models\KtbGroup;
use App\Models\KtbMember;
use App\Models\KtbMemberRelationship;

echo "=== STATISTIK DATABASE KTB ===" . PHP_EOL . PHP_EOL;

// Statistik umum
echo "📊 Total Groups: " . KtbGroup::count() . PHP_EOL;
echo "👥 Total Members: " . KtbMember::count() . PHP_EOL;
echo "🔗 Total Relationships: " . KtbMemberRelationship::count() . PHP_EOL;
echo PHP_EOL;

// Distribusi per generasi
echo "=== DISTRIBUSI PER GENERASI ===" . PHP_EOL;
for ($i = 1; $i <= 3; $i++) {
    $count = KtbMember::where('generation', $i)->count();
    echo "Generation $i: $count orang" . PHP_EOL;
}
echo PHP_EOL;

// Detail Andi
echo "=== STRUKTUR ANDI ===" . PHP_EOL;
$andi = KtbMember::where('name', 'Andi')->first();
if ($andi) {
    echo "Nama: " . $andi->name . PHP_EOL;
    echo "Kelompok: " . $andi->currentGroup->name . PHP_EOL;
    echo "Total Adik KTB Langsung: " . $andi->mentees()->count() . PHP_EOL;

    echo PHP_EOL . "Adik-adik KTB:" . PHP_EOL;
    foreach ($andi->mentees as $mentee) {
        echo "  - " . $mentee->name;
        if ($mentee->hasOpenedNewGroup()) {
            echo " ✓ (sudah multiplikasi, punya " . $mentee->mentees()->count() . " adik)";
        }
        echo PHP_EOL;
    }

    echo PHP_EOL . "Total Keturunan (recursive): " . $andi->getAllDescendants()->count() . " orang" . PHP_EOL;
}
echo PHP_EOL;

// Detail Kelompok
echo "=== DAFTAR KELOMPOK ===" . PHP_EOL;
$groups = KtbGroup::with('leader', 'members')->get();
foreach ($groups as $group) {
    echo "📍 " . $group->name . PHP_EOL;
    echo "   Leader: " . $group->leader->name . PHP_EOL;
    echo "   Anggota: " . $group->members->count() . " orang" . PHP_EOL;
    echo "   Status: " . $group->status . PHP_EOL;
    echo PHP_EOL;
}
