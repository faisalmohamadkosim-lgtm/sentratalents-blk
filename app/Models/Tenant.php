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

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? Storage::url($this->cover_image) : null;
    }
}
