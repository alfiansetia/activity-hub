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
        // ─── Perusahaan / Companies ───
        $companiesData = [
            'PT Teknologi Nusantara',
            'CV Maju Bersama',
            'PT Digital Solusi Indonesia',
            'PT Aneka Industri',
            'CV Kreatif Mandiri',
        ];

        $companies = collect();
        foreach ($companiesData as $name) {
            $companies->push(Company::create(['name' => $name]));
        }

        // ─── Admin ───
        $admin = User::factory()->admin()->create([
            'name'  => 'Administrator',
            'email' => 'admin@activityhub.com',
        ]);

        // ─── Dosen / Pembimbing ───
        $dosen1 = User::factory()->dosen()->create([
            'name'  => 'Dr. Budi Santoso',
            'email' => 'budi@activityhub.com',
        ]);

        $dosen2 = User::factory()->dosen()->create([
            'name'  => 'Dr. Siti Rahayu',
            'email' => 'siti@activityhub.com',
        ]);

        // ─── Pengguna Biasa (approved, terhubung ke perusahaan) ───
        $userNames = [
            'Ahmad Fauzi',
            'Rina Wulandari',
            'Dewi Lestari',
            'Muhammad Rizki',
            'Putri Amelia',
        ];

        $users = collect();
        foreach ($companies as $i => $company) {
            $user = User::factory()->withCompany($company)->create([
                'name'  => $userNames[$i],
                'email' => strtolower(str_replace(' ', '.', $userNames[$i])) . '@gmail.com',
            ]);
            $users->push($user);
        }

        // ─── Pengguna yang belum disetujui ───
        User::factory()->create([
            'name'           => 'Andi Prasetyo',
            'email'          => 'andi.prasetyo@gmail.com',
            'company_status' => 'pending',
            'company_id'     => $companies->first()->id,
        ]);

        // ─── Kegiatan / Activities ───
        $activityTemplates = [
            'accepted' => [
                [
                    'title'        => 'Rapat Koordinasi Divisi IT',
                    'descriptions' => 'Melakukan rapat koordinasi dengan tim IT untuk membahas proyek pengembangan sistem informasi manajemen yang sedang berjalan. Dibahas mengenai progress, kendala, dan rencana minggu depan.',
                    'rules'        => 'Membuat notulensi rapat dan mengirimkan ke seluruh anggota tim.',
                    'tools'        => 'Laptop, proyektor, whiteboard',
                ],
                [
                    'title'        => 'Pengujian Aplikasi Mobile',
                    'descriptions' => 'Melakukan pengujian fungsional dan usability testing pada aplikasi mobile yang baru dikembangkan. Menguji fitur login, navigasi, dan proses input data.',
                    'rules'        => 'Dokumentasikan semua bug yang ditemukan dalam format laporan pengujian.',
                    'tools'        => 'Smartphone Android, laptop, JIRA',
                ],
                [
                    'title'        => 'Presentasi Hasil Analisis Data',
                    'descriptions' => 'Mempresentasikan hasil analisis data penjualan kuartal ketiga kepada manajemen. Data menunjukkan peningkatan penjualan sebesar 15% dibanding kuartal sebelumnya.',
                    'rules'        => 'Siapkan slide presentasi yang menarik dan data yang akurat.',
                    'tools'        => 'PowerPoint, laptop, proyektor',
                ],
                [
                    'title'        => 'Pelatihan Keamanan Siber',
                    'descriptions' => 'Mengikuti pelatihan keamanan siber yang diselenggarakan oleh divisi IT. Materi meliputi pengenalan ancaman siber, cara melindungi data perusahaan, dan praktik terbaik keamanan.',
                    'rules'        => 'Wajib mengikuti seluruh sesi dan mengerjakan post-test.',
                    'tools'        => 'Laptop, akses internet',
                ],
                [
                    'title'        => 'Dokumentasi Proses Bisnis',
                    'descriptions' => 'Mendokumentasikan proses bisnis divisi keuangan dalam bentuk flowchart dan SOP. Wawancara dengan beberapa staf untuk memahami alur kerja yang ada.',
                    'rules'        => 'Dokumen harus disetujui oleh kepala divisi sebelum difinalisasi.',
                    'tools'        => 'Microsoft Visio, laptop',
                ],
            ],
            'rejected' => [
                [
                    'title'        => 'Kunjungan Klien Tanpa Izin',
                    'descriptions' => 'Melakukan kunjungan ke klien tanpa mendapatkan persetujuan dari atasan terlebih dahulu.',
                    'reject_reason' => 'Kunjungan tidak memiliki surat tugas resmi dari perusahaan. Harap ajukan surat tugas terlebih dahulu.',
                ],
                [
                    'title'        => 'Laporan Kegiatan Tidak Lengkap',
                    'descriptions' => 'Menyerahkan laporan kegiatan tanpa dilengkapi dokumentasi foto dan tanda tangan pembimbing.',
                    'reject_reason' => 'Laporan belum lengkap. Mohon lampirkan foto kegiatan dan tanda tangan pembimbing.',
                ],
                [
                    'title'        => 'Pelatihan di Luar Jam Kerja',
                    'descriptions' => 'Mengikuti pelatihan yang diadakan di luar jam kerja tanpa persetujuan overtime dari atasan.',
                    'reject_reason' => 'Kegiatan diluar jam kerja memerlukan persetujuan khusus. Silakan ajukan form overtime.',
                ],
            ],
            'pending' => [
                [
                    'title'        => 'Pembuatan Website Perusahaan',
                    'descriptions' => 'Mengerjakan pembuatan website profil perusahaan dengan menggunakan framework Laravel. Website mencakup halaman utama, tentang kami, layanan, dan kontak.',
                    'rules'        => 'Website harus responsif dan sesuai dengan brand guidelines perusahaan.',
                    'tools'        => 'Laptop, VS Code, Laravel, Figma',
                ],
                [
                    'title'        => 'Migrasi Database ke Cloud',
                    'descriptions' => 'Melakukan migrasi database on-premise ke cloud AWS RDS. Proses meliputi backup data, setup instance, migrasi, dan pengujian.',
                    'rules'        => 'Pastikan zero downtime selama proses migrasi.',
                    'tools'        => 'Laptop, AWS Console, MySQL Workbench',
                ],
                [
                    'title'        => 'Desain Ulang UI Dashboard',
                    'descriptions' => 'Melakukan redesign antarmuka dashboard admin untuk meningkatkan pengalaman pengguna. Menggunakan pendekatan user-centered design dengan wireframe dan prototyping.',
                    'rules'        => 'Desain harus mendapat approval dari product manager sebelum development.',
                    'tools'        => 'Figma, laptop, sticky notes',
                ],
                [
                    'title'        => 'Optimasi Performa Sistem',
                    'descriptions' => 'Melakukan analisis dan optimasi performa sistem yang mengalami penurunan response time. Menggunakan tools profiling untuk mengidentifikasi bottleneck.',
                    'rules'        => 'Dokumentasikan sebelum dan sesudah optimasi untuk perbandingan.',
                    'tools'        => 'Laptop, New Relic, Laravel Telescope',
                ],
                [
                    'title'        => 'Penyusunan Dokumen SOP',
                    'descriptions' => 'Menyusun Standar Operasional Prosedur untuk divisi HRD mencakup proses rekrutmen, onboarding, dan evaluasi kinerja karyawan.',
                    'rules'        => 'SOP harus disetujui oleh HRD Manager dan Direktur.',
                    'tools'        => 'Microsoft Word, laptop',
                ],
            ],
        ];

        foreach ($users as $user) {
            // Kegiatan yang diterima (2 per user)
            $acceptedActivities = collect($activityTemplates['accepted'])->random(2);
            foreach ($acceptedActivities as $template) {
                Activity::create(array_merge($template, [
                    'date'       => fake()->dateTimeBetween('-30 days', '-1 days'),
                    'user_id'    => $user->id,
                    'company_id' => $user->company_id,
                    'status'     => 'accept',
                    'accept_by'  => $dosen1->id,
                    'accept_at'  => fake()->dateTimeBetween('-7 days', 'now'),
                ]));
            }

            // Kegiatan yang ditolak (1 per user)
            $rejectedTemplate = collect($activityTemplates['rejected'])->random();
            Activity::create(array_merge($rejectedTemplate, [
                'date'       => fake()->dateTimeBetween('-30 days', '-7 days'),
                'user_id'    => $user->id,
                'company_id' => $user->company_id,
                'status'     => 'reject',
                'reject_by'  => $dosen2->id,
                'reject_at'  => fake()->dateTimeBetween('-5 days', 'now'),
            ]));

            // Kegiatan yang pending (2 per user)
            $pendingActivities = collect($activityTemplates['pending'])->random(2);
            foreach ($pendingActivities as $template) {
                $activity = Activity::create(array_merge($template, [
                    'date'       => fake()->dateTimeBetween('-7 days', 'now'),
                    'user_id'    => $user->id,
                    'company_id' => $user->company_id,
                    'status'     => 'pending',
                ]));

                // Attachments untuk kegiatan pending
                $attachmentCaptions = [
                    'Foto kegiatan',
                    'Dokumen pendukung',
                    'Bukti partisipasi',
                ];
                Attachment::factory(rand(1, 3))->create([
                    'activity_id' => $activity->id,
                ]);
            }
        }

        $this->command->info('✅ Seeding selesai!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@activityhub.com', 'password'],
                ['Dosen', 'budi@activityhub.com', 'password'],
                ['Dosen', 'siti@activityhub.com', 'password'],
                ['User', 'ahmad.fauzi@gmail.com', 'password'],
                ['User', 'rina.wulandari@gmail.com', 'password'],
                ['User', 'dewi.lestari@gmail.com', 'password'],
                ['User', 'muhammad.rizki@gmail.com', 'password'],
                ['User', 'putri.amelia@gmail.com', 'password'],
                ['Pending', 'andi.prasetyo@gmail.com', 'password'],
            ]
        );
    }
}
