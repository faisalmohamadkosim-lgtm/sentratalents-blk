<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'code', 'description', 'address', 'city', 'province',
        'phone', 'email', 'website', 'logo', 'cover_image', 'established_year',
        'accreditation', 'active',
        'bpjs_kesehatan_rate_employee', 'bpjs_kesehatan_rate_employer', 'bpjs_kesehatan_salary_cap',
        'bpjs_jht_rate_employee', 'bpjs_jht_rate_employer',
        'bpjs_jp_rate_employee', 'bpjs_jp_rate_employer', 'bpjs_jp_salary_cap',
        'bpjs_jkk_rate_employer', 'bpjs_jkm_rate_employer',
        'overtime_multiplier', 'standard_work_days',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'established_year' => 'integer',
        ];
    }

    public function users() { return $this->hasMany(User::class, 'tenant_id'); }
    public function facilities() { return $this->hasMany(Facility::class, 'tenant_id'); }
    public function trainers() { return $this->hasMany(Trainer::class, 'tenant_id'); }
    public function orgStructureMembers() { return $this->hasMany(OrgStructureMember::class, 'tenant_id')->orderBy('sort_order'); }
    public function programs() { return $this->hasMany(Program::class, 'tenant_id'); }
    public function batches() { return $this->hasMany(Batch::class, 'tenant_id'); }
    public function participants() { return $this->hasMany(Participant::class, 'tenant_id'); }
    public function galleries() { return $this->hasMany(TenantGallery::class, 'tenant_id')->orderBy('sort_order'); }
    public function employees() { return $this->hasMany(Employee::class, 'tenant_id'); }
    public function requiredPositions() { return $this->hasMany(RequiredPosition::class, 'tenant_id'); }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? Storage::url($this->cover_image) : null;
    }
}
