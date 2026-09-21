<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BootstrapUltraadmin extends Command
{
    protected $signature = 'app:bootstrap-ultraadmin {email} {name} {password}';

    protected $description = 'Create the first ultraadmin user (not tied to any clinic tenant)';

    public function handle(): int
    {
        if (User::where('email', $this->argument('email'))->exists()) {
            $this->error('Email sudah terdaftar.');
            return self::FAILURE;
        }

        User::create([
            'name'      => $this->argument('name'),
            'email'     => $this->argument('email'),
            'password'  => Hash::make($this->argument('password')),
            'role'      => 'ultraadmin',
            'tenant_id' => null,
            'active'    => true,
        ]);

        $this->info('Ultraadmin berhasil dibuat.');
        return self::SUCCESS;
    }
}
