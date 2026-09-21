<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStage extends Model
{
    protected $fillable = ['program_id', 'title', 'description', 'duration_days', 'sort_order'];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function program() { return $this->belongsTo(Program::class); }
}
