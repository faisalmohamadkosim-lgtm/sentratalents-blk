<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Trainer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'specialization', 'bio', 'photo', 'email', 'phone', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function leadPrograms() { return $this->hasMany(Program::class, 'lead_trainer_id'); }

    public function photoUrl(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }
}
