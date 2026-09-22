@extends('layouts.app')
@section('title','Scan QR Absensi')
@section('content')

<div class="page-header">
  <div class="page-title">📷 Scan QR Absensi</div>
</div>

@include('attendance-scan._scanner', ['handleUrl' => route('attendance-scan.handle')])

@endsection
