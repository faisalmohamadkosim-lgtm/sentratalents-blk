<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

// Berita/pengumuman di landing page utama -- platform-level, dikelola superadmin.
class PlatformNews extends Model
{
    protected $fillable = ['title', 'excerpt', 'image', 'link_url', 'published_at', 'sort_order', 'active'];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'active' => 'boolean',
        ];
    }

    public function imageUrl(): ?string
    {
        if (!$this->image) return null;
        return str_starts_with($this->image, 'http') ? $this->image : Storage::url($this->image);
    }
}
