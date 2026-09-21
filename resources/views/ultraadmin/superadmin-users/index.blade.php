@extends('layouts.ultraadmin')
@section('title', 'Kelola Superadmin')
@section('content')
<div class="page-header">
    <div class="page-title">Kelola Superadmin</div>
    <a href="{{ route('ultraadmin.superadmin-users.create') }}" class="btn btn-primary">Tambah Superadmin</a>
</div>

<div class="card" style="overflow-x:auto;padding:0;">
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge {{ $user->role === 'ultraadmin' ? 'badge-amber' : 'badge-green' }}">{{ ucfirst($user->role) }}</span></td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:var(--text3);">Belum ada akun superadmin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
