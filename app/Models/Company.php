<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'contact_person', 'phone', 'email', 'address', 'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function users() { return $this->hasMany(User::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }
}
