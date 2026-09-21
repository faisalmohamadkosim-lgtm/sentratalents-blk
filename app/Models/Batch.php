<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use BelongsToTenant;

    const STATUSES = ['planned', 'ongoing', 'completed', 'cancelled'];

    protected $fillable = ['tenant_id', 'program_id', 'code', 'start_date', 'end_date', 'quota', 'status'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'quota' => 'integer',
        ];
    }

    public function program() { return $this->belongsTo(Program::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }

    public function participantCount(): int
    {
        return $this->enrollments()->count();
    }
}
