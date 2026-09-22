<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'tenant_id', 'company_id', 'active'];
    protected $hidden   = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active'   => 'boolean',
        ];
    }

    public function tenant() { return $this->belongsTo(Tenant::class, 'tenant_id'); }
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->hasOne(Employee::class); }
    public function trainer() { return $this->hasOne(Trainer::class); }

    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isOwner(): bool      { return $this->role === 'owner'; }
    public function isStaff(): bool      { return $this->role === 'staff'; }
    public function isCompany(): bool    { return $this->role === 'perusahaan'; }
    public function isTrainer(): bool    { return $this->role === 'trainer'; }
    public function isSecurity(): bool   { return $this->role === 'security'; }
    public function isSuperadmin(): bool { return $this->role === 'superadmin'; }
    // Level di atas superadmin, khusus mengelola akun superadmin --
    // dibuat lewat console (app:bootstrap-ultraadmin), bukan lewat UI.
    public function isUltraadmin(): bool { return $this->role === 'ultraadmin'; }
    public function canManage(): bool    { return in_array($this->role, ['admin', 'staff']); }
}
