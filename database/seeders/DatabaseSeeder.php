<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Facility;
use App\Models\OrgStructureMember;
use App\Models\Participant;
use App\Models\Program;
use App\Models\ProgramStage;
use App\Models\RequiredPosition;
use App\Models\Tenant;
use App\Models\TenantGallery;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $firstNames = [
        'Ahmad', 'Budi', 'Siti', 'Dewi', 'Agus', 'Rina', 'Hendra', 'Fitri', 'Joko', 'Wati',
        'Andi', 'Rizki', 'Putri', 'Bayu', 'Lestari', 'Eko', 'Mega', 'Fajar', 'Indah', 'Yusuf',
        'Nurul', 'Dedi', 'Ratna', 'Wahyu', 'Sari', 'Iwan', 'Yuni', 'Arif', 'Diah', 'Taufik',
        'Hesti', 'Rudi', 'Novi', 'Slamet', 'Ika', 'Gunawan', 'Wulan', 'Rahmat', 'Endah', 'Doni',
    ];

    private array $lastNames = [
        'Saputra', 'Pratama', 'Wijaya', 'Kurniawan', 'Setiawan', 'Nugroho', 'Hidayat', 'Susanto',
        'Rahayu', 'Wibowo', 'Santoso', 'Handoko', 'Permana', 'Kusuma', 'Firmansyah', 'Gunadi',
        'Suryani', 'Maulana', 'Puspita', 'Anggraini',
    ];

    // Counter global (lintas tenant) untuk employee_no, karena kolomnya unique di seluruh
    // tabel (bukan per-tenant) -- lihat EmployeeController::generateNo() yang memakai pola sama.
    private int $employeeSeq = 1;

    public function run(): void
    {
        $tenantData = [
            ['name' => 'BLK Bandung', 'city' => 'Bandung', 'province' => 'Jawa Barat', 'slug' => 'blk-bandung'],
            ['name' => 'BLK Surabaya Raya', 'city' => 'Surabaya', 'province' => 'Jawa Timur', 'slug' => 'blk-surabaya-raya'],
            ['name' => 'BLK Yogyakarta', 'city' => 'Yogyakarta', 'province' => 'Daerah Istimewa Yogyakarta', 'slug' => 'blk-yogyakarta'],
        ];

        foreach ($tenantData as $t) {
            $tenant = Tenant::create([
                'name' => $t['name'],
                'slug' => $t['slug'],
                'code' => 'BLK-' . strtoupper(Str::random(5)),
                'description' => "{$t['name']} adalah balai latihan kerja yang menyelenggarakan pelatihan vokasi bagi calon tenaga kerja di {$t['city']} dan sekitarnya.",
                'address' => 'Jl. Pelatihan Kerja No. ' . random_int(1, 99) . ', ' . $t['city'],
                'city' => $t['city'],
                'province' => $t['province'],
                'phone' => '0' . random_int(21, 299) . '-' . random_int(1000000, 9999999),
                'email' => 'info@' . str_replace('-', '', $t['slug']) . '.go.id',
                'website' => 'https://' . str_replace('-', '', $t['slug']) . '.go.id',
                'established_year' => random_int(1985, 2015),
                'accreditation' => collect(['A', 'B', 'Terakreditasi'])->random(),
                'active' => true,
            ]);

            $admin = $this->seedUsers($tenant, $t['slug']);
            $trainers = $this->seedTrainers($tenant, $t['slug']);
            $trainerUser = User::where('tenant_id', $tenant->id)->where('role', 'trainer')->first();
            $this->seedFacilities($tenant);
            $this->seedOrgStructure($tenant);
            $this->seedGallery($tenant, $t['slug']);
            $programs = $this->seedPrograms($tenant, $trainers);
            $batches = $this->seedBatches($tenant, $programs);
            $participants = $this->seedParticipants($tenant);
            $companies = $this->seedCompanies($tenant, $t['slug']);
            $this->seedEnrollments($tenant, $participants, $batches, $companies);

            $employees = $this->seedEmployees($tenant, $admin, $trainerUser);
            $this->seedAttendances($tenant, $employees);
            $this->seedRequiredPositions($tenant);
        }

        $this->seedPlatformGallery($tenantData);
        $this->seedPlatformNews($tenantData);
    }

    // Berita/pengumuman landing page utama -- platform-level, dikelola superadmin lewat
    // Superadmin\PlatformNewsController, tampil sebagai daftar geser horizontal.
    private function seedPlatformNews(array $tenantData): void
    {
        $items = [
            ['title' => 'Pendaftaran Gelombang Baru Dibuka', 'excerpt' => 'BLK di seluruh Indonesia membuka pendaftaran peserta pelatihan vokasi gelombang baru untuk berbagai kejuruan.', 'link' => '/lulusan', 'days_ago' => 2],
            ['title' => 'Kerja Sama dengan Perusahaan Mitra Baru', 'excerpt' => 'Sejumlah perusahaan bergabung sebagai mitra penyalur tenaga kerja lulusan BLK.', 'link' => null, 'days_ago' => 7],
            ['title' => $tenantData[0]['name'] . ' Raih Akreditasi A', 'excerpt' => 'Peningkatan status akreditasi setelah proses penilaian tahunan.', 'link' => '/blk/' . $tenantData[0]['slug'], 'days_ago' => 14],
            ['title' => 'Wisuda Angkatan 2026-A', 'excerpt' => 'Ratusan peserta pelatihan resmi diwisuda dan siap memasuki dunia kerja.', 'link' => '/lulusan', 'days_ago' => 20],
            ['title' => 'Program Pelatihan Baru di ' . $tenantData[1]['name'], 'excerpt' => 'Penambahan program pelatihan baru untuk menjawab kebutuhan industri.', 'link' => '/blk/' . $tenantData[1]['slug'], 'days_ago' => 25],
        ];
        foreach ($items as $i => $item) {
            \App\Models\PlatformNews::create([
                'title' => $item['title'],
                'excerpt' => $item['excerpt'],
                'image' => 'https://picsum.photos/seed/news-' . ($i + 1) . '/500/375',
                'link_url' => $item['link'],
                'published_at' => now()->subDays($item['days_ago']),
                'sort_order' => $i + 1,
                'active' => true,
            ]);
        }
    }

    // Galeri landing page utama -- platform-level (bukan tenant-scoped), dikelola
    // superadmin lewat Superadmin\PlatformGalleryController, masing-masing foto punya
    // tautan sendiri (bisa ke profil BLK tertentu, halaman lulusan, atau URL eksternal).
    private function seedPlatformGallery(array $tenantData): void
    {
        $items = [
            ['caption' => 'Peserta pelatihan siap kerja dari seluruh Indonesia', 'link' => '/blk/' . $tenantData[0]['slug']],
            ['caption' => 'Kunjungan industri mitra ke BLK', 'link' => '/blk/' . $tenantData[1]['slug']],
            ['caption' => 'Wisuda dan penyerahan sertifikat kelulusan', 'link' => '/lulusan'],
            ['caption' => 'Fasilitas pelatihan modern', 'link' => '/blk/' . $tenantData[2]['slug']],
        ];
        foreach ($items as $i => $item) {
            \App\Models\PlatformGalleryPhoto::create([
                'photo' => 'https://picsum.photos/seed/platform-' . ($i + 1) . '/640/480',
                'caption' => $item['caption'],
                'link_url' => $item['link'],
                'sort_order' => $i + 1,
                'active' => true,
            ]);
        }
    }

    private function randomName(): string
    {
        return $this->firstNames[array_rand($this->firstNames)] . ' ' . $this->lastNames[array_rand($this->lastNames)];
    }

    private function seedUsers(Tenant $tenant, string $slug): User
    {
        $domain = str_replace('-', '', $slug) . '.test';

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin ' . $tenant->name,
            'email' => 'admin@' . $domain,
            'password' => Hash::make('Test@123'),
            'role' => 'admin',
            'active' => true,
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Pimpinan ' . $tenant->name,
            'email' => 'owner@' . $domain,
            'password' => Hash::make('Test@123'),
            'role' => 'owner',
            'active' => true,
        ]);

        $staffCount = random_int(1, 2);
        for ($i = 1; $i <= $staffCount; $i++) {
            User::create([
                'tenant_id' => $tenant->id,
                'name' => $this->randomName(),
                'email' => "staff{$i}@{$domain}",
                'password' => Hash::make('Test@123'),
                'role' => 'staff',
                'active' => true,
            ]);
        }

        // Akun demo untuk scan QR absensi -- lihat AttendanceScanController.
        User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Security ' . $tenant->name,
            'email' => 'security@' . $domain,
            'password' => Hash::make('Test@123'),
            'role' => 'security',
            'active' => true,
        ]);

        return $admin;
    }

    private function seedFacilities(Tenant $tenant): void
    {
        $pool = [
            ['name' => 'Bengkel Las & Fabrikasi', 'description' => 'Ruang praktik pengelasan dan fabrikasi logam dengan peralatan standar industri.'],
            ['name' => 'Laboratorium Komputer', 'description' => 'Ruang praktik TIK dengan komputer dan jaringan internet.'],
            ['name' => 'Bengkel Otomotif', 'description' => 'Fasilitas praktik perawatan dan perbaikan kendaraan bermotor.'],
            ['name' => 'Ruang Praktik Tata Busana', 'description' => 'Ruang menjahit lengkap dengan mesin jahit dan bordir.'],
            ['name' => 'Asrama Peserta', 'description' => 'Tempat tinggal bagi peserta pelatihan dari luar kota.'],
            ['name' => 'Ruang Praktik Tata Boga', 'description' => 'Dapur praktik untuk pelatihan kuliner dan tata boga.'],
            ['name' => 'Perpustakaan', 'description' => 'Ruang baca dan referensi modul pelatihan.'],
        ];
        foreach (collect($pool)->shuffle()->take(random_int(3, 5))->values() as $i => $f) {
            Facility::create([
                'tenant_id' => $tenant->id,
                'name' => $f['name'],
                'description' => $f['description'],
                'sort_order' => $i,
            ]);
        }
    }

    private function seedTrainers(Tenant $tenant, string $slug): \Illuminate\Support\Collection
    {
        $specializations = [
            'Las & Fabrikasi Logam', 'Teknik Otomotif', 'Tata Busana', 'Teknologi Informasi',
            'Tata Boga', 'Listrik Industri', 'Desain Grafis', 'Perhotelan',
        ];
        $domain = str_replace('-', '', $slug) . '.test';
        $trainers = collect();
        foreach (range(1, random_int(3, 5)) as $i) {
            $name = $this->randomName();
            $trainers->push(Trainer::create([
                'tenant_id' => $tenant->id,
                'name' => $name,
                'specialization' => $specializations[array_rand($specializations)],
                'bio' => "{$name} berpengalaman sebagai instruktur pelatihan vokasi selama " . random_int(3, 15) . ' tahun.',
                'email' => Str::slug($name) . '@' . Str::slug($tenant->name) . '.test',
                'phone' => '08' . random_int(1000000000, 1999999999),
                'active' => true,
            ]));
        }

        // Akun login demo untuk trainer pertama -- role "trainer", dipakai buat memantau
        // program & angkatan yang mereka ampu lewat portal read-only.
        if ($trainers->isNotEmpty()) {
            $firstTrainer = $trainers->first();
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $firstTrainer->name,
                'email' => 'trainer@' . $domain,
                'password' => Hash::make('Test@123'),
                'role' => 'trainer',
                'active' => true,
            ]);
            $firstTrainer->update(['user_id' => $user->id]);
        }

        return $trainers;
    }

    private function seedGallery(Tenant $tenant, string $slug): void
    {
        // Pakai layanan placeholder gambar (picsum.photos, seed per-BLK+index supaya
        // konsisten tiap reseed) karena tidak ada file foto asli yang bisa diunggah di sini --
        // TenantGallery::photoUrl() sudah mendukung URL eksternal langsung.
        $captions = [
            'Praktik lapangan peserta pelatihan',
            'Suasana kelas teori',
            'Kunjungan industri mitra',
            'Uji kompetensi peserta',
            'Kegiatan pembukaan angkatan baru',
        ];
        foreach (range(1, random_int(3, 5)) as $i) {
            TenantGallery::create([
                'tenant_id' => $tenant->id,
                'photo' => "https://picsum.photos/seed/{$slug}-{$i}/640/480",
                'caption' => $captions[array_rand($captions)],
                'sort_order' => $i,
            ]);
        }
    }

    private function seedOrgStructure(Tenant $tenant): void
    {
        $positions = ['Kepala BLK', 'Kepala Tata Usaha', 'Kepala Bidang Pelatihan', 'Kepala Bidang Sertifikasi', 'Staf Kepegawaian'];
        foreach ($positions as $i => $position) {
            if ($i >= random_int(3, 5)) break;
            OrgStructureMember::create([
                'tenant_id' => $tenant->id,
                'name' => $this->randomName(),
                'position' => $position,
                'sort_order' => $i,
            ]);
        }
    }

    private function seedPrograms(Tenant $tenant, \Illuminate\Support\Collection $trainers): \Illuminate\Support\Collection
    {
        $pool = [
            ['name' => 'Teknik Las', 'category' => 'Teknik Manufaktur'],
            ['name' => 'Teknik Sepeda Motor', 'category' => 'Otomotif'],
            ['name' => 'Tata Busana', 'category' => 'Fashion & Garmen'],
            ['name' => 'Desain Grafis', 'category' => 'Teknologi Informasi'],
            ['name' => 'Tata Boga', 'category' => 'Pariwisata & Kuliner'],
            ['name' => 'Instalasi Listrik', 'category' => 'Ketenagalistrikan'],
        ];

        $stagePool = ['Pengenalan K3', 'Dasar Teori', 'Praktik Dasar', 'Praktik Lanjutan', 'Uji Kompetensi', 'Praktik Kerja Lapangan'];

        $programs = collect();
        foreach (collect($pool)->shuffle()->take(random_int(2, 4))->values() as $i => $p) {
            // Program pertama sengaja diampu trainer pertama (yang ditautkan ke akun login
            // demo di seedTrainers()), supaya portal trainer langsung ada datanya waktu diuji.
            $program = Program::create([
                'tenant_id' => $tenant->id,
                'lead_trainer_id' => $trainers->isEmpty() ? null : ($i === 0 ? $trainers->first()->id : $trainers->random()->id),
                'name' => $p['name'],
                'category' => $p['category'],
                'description' => "Program pelatihan {$p['name']} untuk membekali peserta dengan keterampilan siap kerja.",
                'duration_days' => collect([30, 45, 60, 90])->random(),
                'quota' => collect([20, 24, 30])->random(),
                'active' => true,
            ]);

            $stages = collect($stagePool)->shuffle()->take(random_int(2, 4))->values();
            foreach ($stages as $i => $title) {
                ProgramStage::create([
                    'program_id' => $program->id,
                    'title' => $title,
                    'description' => "Tahapan {$title} pada program {$p['name']}.",
                    'duration_days' => collect([3, 5, 7, 10])->random(),
                    'sort_order' => $i,
                ]);
            }

            $programs->push($program);
        }
        return $programs;
    }

    private function seedBatches(Tenant $tenant, \Illuminate\Support\Collection $programs): \Illuminate\Support\Collection
    {
        $batches = collect();
        foreach ($programs as $program) {
            $statuses = collect(['completed', 'ongoing', 'planned'])->shuffle();
            foreach (range(1, random_int(2, 3)) as $i) {
                $status = $statuses[$i - 1] ?? 'planned';
                $start = match ($status) {
                    'completed' => now()->subMonths(random_int(4, 12))->startOfMonth(),
                    'ongoing' => now()->subDays(random_int(5, 20)),
                    default => now()->addDays(random_int(10, 60)),
                };
                $end = (clone $start)->addDays($program->duration_days ?? 45);

                $batches->push(Batch::create([
                    'tenant_id' => $tenant->id,
                    'program_id' => $program->id,
                    'code' => $start->format('Y') . '-' . chr(64 + $i),
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'quota' => $program->quota,
                    'status' => $status,
                ]));
            }
        }
        return $batches;
    }

    private function seedParticipants(Tenant $tenant): \Illuminate\Support\Collection
    {
        $educations = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1'];
        $participants = collect();
        foreach (range(1, random_int(15, 30)) as $i) {
            $name = $this->randomName();
            $participants->push(Participant::create([
                'tenant_id' => $tenant->id,
                'name' => $name,
                'nik' => (string) random_int(3100000000000000, 3999999999999999),
                'gender' => random_int(0, 1) ? 'L' : 'P',
                'birth_date' => now()->subYears(random_int(18, 35))->subDays(random_int(0, 365))->toDateString(),
                'address' => 'Jl. Merdeka No. ' . random_int(1, 150) . ', ' . $tenant->city,
                'phone' => '08' . random_int(1000000000, 1999999999),
                'email' => Str::slug($name) . random_int(1, 999) . '@mail.test',
                'education' => $educations[array_rand($educations)],
            ]));
        }
        return $participants;
    }

    private function seedCompanies(Tenant $tenant, string $slug): \Illuminate\Support\Collection
    {
        $pool = [
            ['name' => 'PT Karya Mandiri Sejahtera', 'contact_person' => 'Bambang Setiawan'],
            ['name' => 'PT Sumber Rezeki Industri', 'contact_person' => 'Rina Kusuma'],
            ['name' => 'CV Maju Bersama Teknik', 'contact_person' => 'Hadi Purnomo'],
        ];
        $domain = str_replace('-', '', $slug) . '.test';

        $companies = collect(collect($pool)->shuffle()->take(2)->values())
            ->map(fn ($c, $i) => Company::create([
                'tenant_id' => $tenant->id,
                'name' => $c['name'],
                'contact_person' => $c['contact_person'],
                'phone' => '08' . random_int(1000000000, 1999999999),
                'email' => 'hrd' . ($i + 1) . '@' . Str::slug($c['name']) . '.test',
                'address' => 'Kawasan Industri ' . $tenant->city . ' Blok ' . chr(65 + $i),
                'active' => true,
            ]));

        // Akun login demo untuk perwakilan perusahaan pertama -- role "perusahaan",
        // dipakai buat memantau status peserta yang dikirim lewat portal read-only.
        if ($companies->isNotEmpty()) {
            User::create([
                'tenant_id' => $tenant->id,
                'company_id' => $companies->first()->id,
                'name' => $companies->first()->contact_person ?? 'HRD ' . $companies->first()->name,
                'email' => 'perusahaan@' . $domain,
                'password' => Hash::make('Test@123'),
                'role' => 'perusahaan',
                'active' => true,
            ]);
        }

        return $companies;
    }

    private function seedEnrollments(Tenant $tenant, \Illuminate\Support\Collection $participants, \Illuminate\Support\Collection $batches, \Illuminate\Support\Collection $companies): void
    {
        if ($batches->isEmpty() || $participants->isEmpty()) return;

        $participants = $participants->shuffle();
        $i = 0;

        foreach ($batches as $batch) {
            $slotCount = min(random_int(4, 9), $participants->count() - $i);
            if ($slotCount <= 0) break;

            for ($k = 0; $k < $slotCount; $k++) {
                $participant = $participants[$i];
                $i++;

                if ($batch->status === 'completed') {
                    // Sebagian besar peserta batch selesai berstatus lulus, supaya daftar
                    // lulusan langsung bisa didemokan; sisanya realistis "dropped_out".
                    $status = random_int(1, 100) <= 80 ? 'graduated' : 'dropped_out';
                } elseif ($batch->status === 'ongoing') {
                    $status = 'active';
                } else {
                    $status = 'registered';
                }

                $graduationDate = null;
                $finalScore = null;
                $certificateNo = null;
                if ($status === 'graduated') {
                    $graduationDate = $batch->end_date ?? now()->subMonths(random_int(1, 6));
                    $finalScore = random_int(70, 98);
                    $certificateNo = 'CERT-' . $batch->code . '-' . str_pad((string) ($k + 1), 3, '0', STR_PAD_LEFT);
                }

                // Sekitar 30% peserta dikirim oleh perusahaan mitra, sisanya mandiri.
                $sponsoredByCompany = $companies->isNotEmpty() && random_int(1, 100) <= 30;

                Enrollment::create([
                    'tenant_id' => $tenant->id,
                    'participant_id' => $participant->id,
                    'batch_id' => $batch->id,
                    'status' => $status,
                    'registration_type' => $sponsoredByCompany ? 'perusahaan' : 'mandiri',
                    'company_id' => $sponsoredByCompany ? $companies->random()->id : null,
                    'final_score' => $finalScore,
                    'graduation_date' => $graduationDate,
                    'certificate_no' => $certificateNo,
                ]);
            }
        }
    }

    // Karyawan (HR/Payroll) -- beberapa jabatan khas BLK, pegawai pertama ditautkan ke
    // akun admin tenant supaya halaman self-service (Absensi Saya/Slip Gaji Saya) punya
    // data begitu login sebagai admin@<tenant>.test.
    private function seedEmployees(Tenant $tenant, User $adminUser, ?User $trainerUser = null): \Illuminate\Support\Collection
    {
        $positionPool = [
            ['position' => 'Kepala BLK', 'employment_type' => 'tetap', 'base_salary' => 6500000, 'allowance_position' => 750000],
            ['position' => 'Kepala Tata Usaha', 'employment_type' => 'tetap', 'base_salary' => 5500000, 'allowance_position' => 400000],
            ['position' => 'Instruktur/Trainer', 'employment_type' => 'tetap', 'base_salary' => 4800000, 'allowance_position' => 0],
            ['position' => 'Tenaga Administrasi', 'employment_type' => 'kontrak', 'base_salary' => 3500000, 'allowance_position' => 0],
            ['position' => 'Staf Keuangan', 'employment_type' => 'tetap', 'base_salary' => 4000000, 'allowance_position' => 0],
            ['position' => 'Petugas Kebersihan', 'employment_type' => 'harian', 'base_salary' => 2800000, 'allowance_position' => 0],
        ];
        $ptkpOptions = Employee::ptkpOptions();
        $year = now()->year;
        $employees = collect();

        foreach (collect($positionPool)->take(random_int(4, 6))->values() as $i => $p) {
            $isAdminLink = $i === 0;
            // Slot "Instruktur/Trainer" ditautkan ke akun login trainer demo (kalau ada)
            // supaya menu Absensi Saya di portal trainer langsung ada datanya saat diuji.
            $isTrainerLink = !$isAdminLink && $trainerUser && $p['position'] === 'Instruktur/Trainer';
            $linkedUser = $isAdminLink ? $adminUser : ($isTrainerLink ? $trainerUser : null);
            $name = $linkedUser?->name ?? $this->randomName();

            $employees->push(Employee::create([
                'tenant_id' => $tenant->id,
                'user_id' => $linkedUser?->id,
                'employee_no' => 'EMP-' . $year . '-' . str_pad((string) $this->employeeSeq++, 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'nik' => (string) random_int(3100000000000000, 3999999999999999),
                'position' => $p['position'],
                'employment_type' => $p['employment_type'],
                'ptkp_status' => $ptkpOptions[array_rand($ptkpOptions)],
                'join_date' => now()->subYears(random_int(1, 8))->subDays(random_int(0, 300)),
                'status' => random_int(1, 100) <= 85 ? 'active' : 'inactive',
                'base_salary' => $p['base_salary'],
                'allowance_position' => $p['allowance_position'],
                'allowance_transport' => 300000,
                'allowance_meal' => 250000,
                'phone' => '08' . random_int(1000000000, 1999999999),
                'email' => Str::slug($name) . random_int(1, 999) . '@' . Str::slug($tenant->name) . '.test',
                'bpjs_kesehatan_active' => true,
                'bpjs_tk_active' => true,
            ]));
        }

        return $employees;
    }

    // Absensi bulan berjalan -- data secukupnya (bukan tiap hari) supaya halaman rekap
    // absensi (attendances.index) langsung ada isinya saat pertama dibuka.
    private function seedAttendances(Tenant $tenant, \Illuminate\Support\Collection $employees): void
    {
        $statusPool = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpha'];
        $daysElapsed = max(1, now()->day - 1);

        foreach ($employees->where('status', 'active') as $employee) {
            $takeCount = min($daysElapsed, random_int(6, 10));
            $days = collect(range(1, $daysElapsed))->shuffle()->take($takeCount)->sort()->values();

            foreach ($days as $d) {
                $date = now()->startOfMonth()->addDays($d - 1);
                if ($date->isWeekend()) continue;

                $status = $statusPool[array_rand($statusPool)];
                Attendance::create([
                    'tenant_id' => $tenant->id,
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'clock_in' => $status === 'hadir' ? $date->copy()->setTime(8, random_int(0, 30)) : null,
                    'clock_out' => $status === 'hadir' ? $date->copy()->setTime(17, random_int(0, 30)) : null,
                    'status' => $status,
                    'overtime_hours' => ($status === 'hadir' && random_int(1, 100) <= 15) ? collect([1, 2, 3])->random() : 0,
                ]);
            }
        }
    }

    // Jabatan Minimal -- sebagian dari template default BLK + satu contoh tambahan,
    // supaya halaman required-positions.index tidak kosong saat pertama dibuka.
    private function seedRequiredPositions(Tenant $tenant): void
    {
        $fromTemplate = collect(RequiredPosition::defaultTemplate())->shuffle()->take(2)->values();
        foreach ($fromTemplate as $tpl) {
            RequiredPosition::create($tpl + ['tenant_id' => $tenant->id]);
        }

        RequiredPosition::create([
            'tenant_id' => $tenant->id,
            'position_name' => 'Teknisi Bengkel/Laboratorium',
            'min_count' => 1,
            'notes' => 'Sesuai jumlah bengkel/laboratorium yang beroperasi.',
            'active' => true,
        ]);
    }
}
