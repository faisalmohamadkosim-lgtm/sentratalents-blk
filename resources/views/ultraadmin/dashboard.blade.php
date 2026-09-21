@extends('layouts.ultraadmin')
@section('title', 'Dashboard')
@section('content')
<div class="page-header">
    <div class="page-title">Dashboard</div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">Total Superadmin</div>
        <div class="stat-value">{{ $totalSuperadmins }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Ultraadmin</div>
        <div class="stat-value">{{ $totalUltraadmins }}</div>
    </div>
</div>

<div class="stat-grid">
    <a href="{{ route('ultraadmin.superadmin-users.index') }}" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-title" style="margin-bottom:.4rem;">Kelola Superadmin</div>
        <p class="text-muted">Tambah dan lihat akun superadmin.</p>
    </a>
</div>
@endsection
