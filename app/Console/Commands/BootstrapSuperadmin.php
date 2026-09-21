<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BootstrapSuperadmin extends Command
{
    protected $signature = 'app:bootstrap-superadmin {email} {name} {password}';

    protected $description = 'Create the first superadmin user (not tied to any clinic tenant)';

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
            'role'      => 'superadmin',
            'tenant_id' => null,
            'active'    => true,
        ]);

        $this->info('Superadmin berhasil dibuat.');
        return self::SUCCESS;
    }
}
