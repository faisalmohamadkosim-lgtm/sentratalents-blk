<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

// Galeri landing page utama -- platform-level, dikelola superadmin, tidak terikat tenant.
class PlatformGalleryPhoto extends Model
{
    protected $fillable = ['photo', 'caption', 'link_url', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function photoUrl(): ?string
    {
        if (!$this->photo) return null;
        return str_starts_with($this->photo, 'http') ? $this->photo : Storage::url($this->photo);
    }
}
