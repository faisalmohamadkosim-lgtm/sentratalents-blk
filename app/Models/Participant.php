<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Participant extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'nik', 'gender', 'birth_date', 'address', 'phone',
        'email', 'education', 'photo',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function enrollments() { return $this->hasMany(Enrollment::class); }

    public function latestEnrollment()
    {
        return $this->hasOne(Enrollment::class)->latestOfMany();
    }

    public function graduatedEnrollments()
    {
        return $this->enrollments()->graduated();
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }
}
