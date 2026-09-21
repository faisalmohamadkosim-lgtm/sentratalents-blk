<?php

namespace App\Http\Controllers;

use App\Models\OrgStructureMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrgStructureMemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $members = OrgStructureMember::when($search, fn ($q) => $q->where('name', 'like', "%$search%")->orWhere('position', 'like', "%$search%"))
            ->orderBy('sort_order')->orderBy('name')
            ->paginate(20)->withQueryString();
        return view('org-structure.index', compact('members', 'search'));
    }

    public function create()
    {
        return view('org-structure.form');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handlePhoto($request, $data);
        OrgStructureMember::create($data);
        return redirect()->route('org-structure.index')->with('success', 'Anggota struktur berhasil ditambahkan.');
    }

    public function edit(OrgStructureMember $orgStructure)
    {
        return view('org-structure.form', ['member' => $orgStructure]);
    }

    public function update(Request $request, OrgStructureMember $orgStructure)
    {
        $data = $this->validated($request);
        if ($request->boolean('delete_photo')) {
            $this->deletePhoto($orgStructure->photo);
            $data['photo'] = null;
        } else {
            $data = $this->handlePhoto($request, $data, $orgStructure->photo);
        }
        $orgStructure->update($data);
        return redirect()->route('org-structure.index')->with('success', 'Anggota struktur berhasil diperbarui.');
    }

    public function destroy(OrgStructureMember $orgStructure)
    {
        $this->deletePhoto($orgStructure->photo);
        $orgStructure->delete();
        return redirect()->route('org-structure.index')->with('success', 'Anggota struktur dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'       => 'required|string|max:150',
            'position'   => 'required|string|max:150',
            'sort_order' => 'nullable|integer|min:0',
            'photo'      => 'nullable|image|max:2048',
        ]);
    }

    private function handlePhoto(Request $request, array $data, ?string $existing = null): array
    {
        if ($request->hasFile('photo')) {
            if ($existing) $this->deletePhoto($existing);
            $data['photo'] = $request->file('photo')->store('org-structure', 'public');
        } else {
            unset($data['photo']);
        }
        return $data;
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
