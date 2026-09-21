<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Program extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'lead_trainer_id', 'name', 'category', 'description',
        'duration_days', 'quota', 'photo', 'active',
    ];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'quota' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function leadTrainer() { return $this->belongsTo(Trainer::class, 'lead_trainer_id'); }
    public function stages() { return $this->hasMany(ProgramStage::class)->orderBy('sort_order'); }
    public function batches() { return $this->hasMany(Batch::class); }

    public function photoUrl(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }
}
