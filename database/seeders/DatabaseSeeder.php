<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Attachment;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Companies ───
        $companies = Company::factory(5)->create();

        // ─── Admin ───
        $admin = User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@test.com',
        ]);

        // ─── Dosen / Lecturers ───
        $dosen1 = User::factory()->dosen()->create([
            'name'  => 'Dosen Satu',
            'email' => 'dosen@test.com',
        ]);

        $dosen2 = User::factory()->dosen()->create([
            'name'  => 'Dosen Dua',
            'email' => 'dosen2@test.com',
        ]);

        // ─── Regular Users (approved, linked to companies) ───
        $users = collect();
        foreach ($companies as $i => $company) {
            $user = User::factory()->withCompany($company)->create([
                'name'  => "User " . ($i + 1),
                'email' => "user" . ($i + 1) . "@test.com",
            ]);
            $users->push($user);
        }

        // Pending user (not yet approved)
        User::factory()->create([
            'name'           => 'Pending User',
            'email'          => 'pending@test.com',
            'company_status' => 'pending',
            'company_id'     => $companies->first()->id,
        ]);

        // ─── Activities ───
        foreach ($users as $user) {
            // Some accepted
            Activity::factory(2)->accepted()->create([
                'user_id'    => $user->id,
                'company_id' => $user->company_id,
                'accept_by'  => $dosen1->id,
            ]);

            // Some rejected
            Activity::factory(1)->rejected()->create([
                'user_id'    => $user->id,
                'company_id' => $user->company_id,
                'reject_by'  => $dosen2->id,
            ]);

            // Some pending
            $pending = Activity::factory(2)->create([
                'user_id'    => $user->id,
                'company_id' => $user->company_id,
                'status'     => 'pending',
            ]);

            // Attachments for some activities
            foreach ($pending as $activity) {
                Attachment::factory(rand(1, 3))->create();
            }
        }

        $this->command->info('✅ Seeding complete!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@test.com', 'password'],
                ['Dosen', 'dosen@test.com', 'password'],
                ['User 1–5', 'user1@test.com – user5@test.com', 'password'],
                ['Pending', 'pending@test.com', 'password'],
            ]
        );
    }
}
