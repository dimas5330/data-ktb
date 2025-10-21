<?php

namespace Database\Seeders;

use App\Models\KtbGroup;
use App\Models\KtbMember;
use App\Models\KtbMemberRelationship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class KtbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Contoh: Andi memiliki 4 adik KTB (A, B, C, D)
     * A membuka kelompok baru dengan 3 adik (E, F, G)
     * B membuka kelompok baru dengan 2 adik (H, I)
     */
    public function run(): void
    {
        // Create Andi (Generation 1 - Founder)
        $andi = KtbMember::create([
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'phone' => '08123456789',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1990-01-15'),
            'gender' => 'male',
            'is_leader' => true,
            'joined_at' => Carbon::parse('2020-01-01'),
            'generation' => 1,
            'status' => 'active',
        ]);

        // Create Kelompok Andi
        $kelompokAndi = KtbGroup::create([
            'name' => 'Kelompok Andi',
            'leader_id' => $andi->id,
            'description' => 'Kelompok KTB pertama yang dipimpin oleh Andi',
            'location' => 'Jakarta Selatan',
            'meeting_day' => 'Jumat',
            'meeting_time' => '19:00:00',
            'status' => 'active',
            'started_at' => Carbon::parse('2020-01-01'),
        ]);

        // Update Andi's current group
        $andi->update(['current_group_id' => $kelompokAndi->id]);

        // Create Generation 2 - Adik KTB Andi (A, B, C, D)
        $memberA = KtbMember::create([
            'name' => 'A',
            'email' => 'a@example.com',
            'phone' => '08123456790',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1992-03-20'),
            'gender' => 'male',
            'current_group_id' => $kelompokAndi->id,
            'is_leader' => true, // A akan membuka kelompok baru
            'joined_at' => Carbon::parse('2020-02-01'),
            'generation' => 2,
            'status' => 'active',
        ]);

        $memberB = KtbMember::create([
            'name' => 'B',
            'email' => 'b@example.com',
            'phone' => '08123456791',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1993-05-10'),
            'gender' => 'female',
            'current_group_id' => $kelompokAndi->id,
            'is_leader' => true, // B akan membuka kelompok baru
            'joined_at' => Carbon::parse('2020-03-01'),
            'generation' => 2,
            'status' => 'active',
        ]);

        $memberC = KtbMember::create([
            'name' => 'C',
            'email' => 'c@example.com',
            'phone' => '08123456792',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1994-07-15'),
            'gender' => 'male',
            'current_group_id' => $kelompokAndi->id,
            'is_leader' => false,
            'joined_at' => Carbon::parse('2020-04-01'),
            'generation' => 2,
            'status' => 'active',
        ]);

        $memberD = KtbMember::create([
            'name' => 'D',
            'email' => 'd@example.com',
            'phone' => '08123456793',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1995-09-25'),
            'gender' => 'female',
            'current_group_id' => $kelompokAndi->id,
            'is_leader' => false,
            'joined_at' => Carbon::parse('2020-05-01'),
            'generation' => 2,
            'status' => 'active',
        ]);

        // Create relationships: Andi -> A, B, C, D
        KtbMemberRelationship::create([
            'mentor_id' => $andi->id,
            'mentee_id' => $memberA->id,
            'group_id' => $kelompokAndi->id,
            'started_at' => Carbon::parse('2020-02-01'),
            'status' => 'active',
        ]);

        KtbMemberRelationship::create([
            'mentor_id' => $andi->id,
            'mentee_id' => $memberB->id,
            'group_id' => $kelompokAndi->id,
            'started_at' => Carbon::parse('2020-03-01'),
            'status' => 'active',
        ]);

        KtbMemberRelationship::create([
            'mentor_id' => $andi->id,
            'mentee_id' => $memberC->id,
            'group_id' => $kelompokAndi->id,
            'started_at' => Carbon::parse('2020-04-01'),
            'status' => 'active',
        ]);

        KtbMemberRelationship::create([
            'mentor_id' => $andi->id,
            'mentee_id' => $memberD->id,
            'group_id' => $kelompokAndi->id,
            'started_at' => Carbon::parse('2020-05-01'),
            'status' => 'active',
        ]);

        // Create Kelompok A (A membuka kelompok baru)
        $kelompokA = KtbGroup::create([
            'name' => 'Kelompok A',
            'leader_id' => $memberA->id,
            'description' => 'Kelompok KTB yang dipimpin oleh A (multiplikasi dari Kelompok Andi)',
            'location' => 'Jakarta Timur',
            'meeting_day' => 'Kamis',
            'meeting_time' => '19:30:00',
            'status' => 'active',
            'started_at' => Carbon::parse('2021-01-15'),
        ]);

        // Create Generation 3 - Adik KTB dari A (E, F, G)
        $memberE = KtbMember::create([
            'name' => 'E',
            'email' => 'e@example.com',
            'phone' => '08123456794',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1996-02-10'),
            'gender' => 'male',
            'current_group_id' => $kelompokA->id,
            'is_leader' => false,
            'joined_at' => Carbon::parse('2021-02-01'),
            'generation' => 3,
            'status' => 'active',
        ]);

        $memberF = KtbMember::create([
            'name' => 'F',
            'email' => 'f@example.com',
            'phone' => '08123456795',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1997-04-20'),
            'gender' => 'female',
            'current_group_id' => $kelompokA->id,
            'is_leader' => false,
            'joined_at' => Carbon::parse('2021-03-01'),
            'generation' => 3,
            'status' => 'active',
        ]);

        $memberG = KtbMember::create([
            'name' => 'G',
            'email' => 'g@example.com',
            'phone' => '08123456796',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1998-06-30'),
            'gender' => 'male',
            'current_group_id' => $kelompokA->id,
            'is_leader' => false,
            'joined_at' => Carbon::parse('2021-04-01'),
            'generation' => 3,
            'status' => 'active',
        ]);

        // Create relationships: A -> E, F, G
        KtbMemberRelationship::create([
            'mentor_id' => $memberA->id,
            'mentee_id' => $memberE->id,
            'group_id' => $kelompokA->id,
            'started_at' => Carbon::parse('2021-02-01'),
            'status' => 'active',
        ]);

        KtbMemberRelationship::create([
            'mentor_id' => $memberA->id,
            'mentee_id' => $memberF->id,
            'group_id' => $kelompokA->id,
            'started_at' => Carbon::parse('2021-03-01'),
            'status' => 'active',
        ]);

        KtbMemberRelationship::create([
            'mentor_id' => $memberA->id,
            'mentee_id' => $memberG->id,
            'group_id' => $kelompokA->id,
            'started_at' => Carbon::parse('2021-04-01'),
            'status' => 'active',
        ]);

        // Create Kelompok B (B membuka kelompok baru)
        $kelompokB = KtbGroup::create([
            'name' => 'Kelompok B',
            'leader_id' => $memberB->id,
            'description' => 'Kelompok KTB yang dipimpin oleh B (multiplikasi dari Kelompok Andi)',
            'location' => 'Jakarta Barat',
            'meeting_day' => 'Rabu',
            'meeting_time' => '18:00:00',
            'status' => 'active',
            'started_at' => Carbon::parse('2021-06-01'),
        ]);

        // Create Generation 3 - Adik KTB dari B (H, I)
        $memberH = KtbMember::create([
            'name' => 'H',
            'email' => 'h@example.com',
            'phone' => '08123456797',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('1999-08-15'),
            'gender' => 'female',
            'current_group_id' => $kelompokB->id,
            'is_leader' => false,
            'joined_at' => Carbon::parse('2021-07-01'),
            'generation' => 3,
            'status' => 'active',
        ]);

        $memberI = KtbMember::create([
            'name' => 'I',
            'email' => 'i@example.com',
            'phone' => '08123456798',
            'address' => 'Jakarta',
            'birth_date' => Carbon::parse('2000-10-05'),
            'gender' => 'male',
            'current_group_id' => $kelompokB->id,
            'is_leader' => false,
            'joined_at' => Carbon::parse('2021-08-01'),
            'generation' => 3,
            'status' => 'active',
        ]);

        // Create relationships: B -> H, I
        KtbMemberRelationship::create([
            'mentor_id' => $memberB->id,
            'mentee_id' => $memberH->id,
            'group_id' => $kelompokB->id,
            'started_at' => Carbon::parse('2021-07-01'),
            'status' => 'active',
        ]);

        KtbMemberRelationship::create([
            'mentor_id' => $memberB->id,
            'mentee_id' => $memberI->id,
            'group_id' => $kelompokB->id,
            'started_at' => Carbon::parse('2021-08-01'),
            'status' => 'active',
        ]);

        $this->command->info('✅ Data KTB berhasil di-seed!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - 3 Kelompok KTB');
        $this->command->info('   - 10 Anggota KTB (1 Gen-1, 4 Gen-2, 5 Gen-3)');
        $this->command->info('   - 9 Relasi mentoring');
    }
}
