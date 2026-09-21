<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use BelongsToTenant;

    const STATUSES = ['registered', 'active', 'graduated', 'dropped_out'];
    const REGISTRATION_TYPES = ['mandiri', 'perusahaan'];

    protected $fillable = [
        'tenant_id', 'participant_id', 'batch_id', 'status', 'final_score',
        'graduation_date', 'certificate_no', 'notes',
        'registration_type', 'company_id',
    ];

    protected function casts(): array
    {
        return [
            'graduation_date' => 'date',
            'final_score' => 'decimal:2',
        ];
    }

    public function participant() { return $this->belongsTo(Participant::class); }
    public function batch() { return $this->belongsTo(Batch::class); }
    public function company() { return $this->belongsTo(Company::class); }

    public function scopeGraduated(Builder $query): Builder
    {
        return $query->where('status', 'graduated');
    }

    public function isCompanySponsored(): bool
    {
        return $this->registration_type === 'perusahaan';
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'registered'  => 'badge-blue',
            'active'      => 'badge-yellow',
            'graduated'   => 'badge-green',
            'dropped_out' => 'badge-red',
            default       => 'badge-gray',
        };
    }
}
