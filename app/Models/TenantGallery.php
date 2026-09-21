<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TenantGallery extends Model
{
    use BelongsToTenant;

    protected $table = 'tenant_galleries';

    protected $fillable = ['tenant_id', 'photo', 'caption', 'sort_order'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function photoUrl(): ?string
    {
        if (!$this->photo) return null;
        return str_starts_with($this->photo, 'http') ? $this->photo : Storage::url($this->photo);
    }
}
