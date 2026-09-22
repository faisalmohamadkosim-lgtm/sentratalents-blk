@extends('layouts.app')
@section('title', isset($employee) ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('content')
@php $isEdit = isset($employee); @endphp

<div class="page-header">
  <div class="page-title">{{ $isEdit ? 'Edit Karyawan' : 'Tambah Karyawan' }}</div>
  <a href="{{ route('employees.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
</div>

<div class="card">
  <form method="POST" action="{{ $isEdit ? route('employees.update', $employee) : route('employees.store') }}" enctype="multipart/form-data">
    @csrf @if($isEdit) @method('PUT') @endif

    <div class="card-title">Data Pokok</div>
    <div class="form-grid">
      <div class="form-group full">
        <label>Nama Lengkap *</label>
        <input type="text" name="name" value="{{ old('name', $employee->name ?? '') }}" required>
      </div>
      <div class="form-group full">
        <label>Foto</label>
        @if($isEdit && $employee->photoUrl())
        <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.75rem">
          <img src="{{ $employee->photoUrl() }}" style="height:56px;width:56px;object-fit:cover;border-radius:50%">
          <label style="display:flex;align-items:center;gap:.3rem;font-size:.8rem;cursor:pointer">
            <input type="checkbox" name="delete_photo" value="1"> Hapus foto
          </label>
        </div>
        @endif
        <input type="file" name="photo" accept="image/*">
        <div class="text-muted" style="margin-top:.3rem">Ditampilkan di hasil scan QR absensi, supaya petugas security bisa mencocokkan wajah. Maks 2MB.</div>
      </div>
      <div class="form-group">
        <label>Akun Login Terkait (opsional)</label>
        <select name="user_id">
          <option value="">-- Tidak Ada --</option>
          @foreach($users as $u)
          <option value="{{ $u->id }}" {{ old('user_id', $employee->user_id ?? request('user_id', '')) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
          @endforeach
        </select>
      </div>
      @php
        $currentPosition = old('position', $employee->position ?? '');
        $isOtherPosition = $currentPosition !== '' && ! in_array($currentPosition, $positionOptions, true);
        $selectedPosition = old('position', $isOtherPosition ? '__other__' : $currentPosition);
      @endphp
      <div class="form-group">
        <label>Jabatan *</label>
        <select name="position" id="position-select" onchange="document.getElementById('position-other-wrap').hidden = (this.value !== '__other__')" required>
          <option value="">-- Pilih Jabatan --</option>
          @foreach($positionOptions as $opt)
          <option value="{{ $opt }}" {{ $selectedPosition === $opt ? 'selected' : '' }}>{{ $opt }}</option>
          @endforeach
          <option value="__other__" {{ $selectedPosition === '__other__' ? 'selected' : '' }}>-- Lainnya (isi sendiri) --</option>
        </select>
      </div>
      <div class="form-group" id="position-other-wrap" {{ $selectedPosition === '__other__' ? '' : 'hidden' }}>
        <label>Sebutkan Jabatan Lain *</label>
        <input type="text" name="position_other" value="{{ old('position_other', $isOtherPosition ? $currentPosition : '') }}">
      </div>
      <div class="form-group">
        <label>Departemen</label>
        <input type="text" name="department" value="{{ old('department', $employee->department ?? '') }}" placeholder="Contoh: Pelatihan, Tata Usaha, Administrasi">
      </div>
      <div class="form-group">
        <label>Tipe Kepegawaian *</label>
        <select name="employment_type" required>
          @foreach(['tetap'=>'Karyawan Tetap','kontrak'=>'Kontrak','harian'=>'Harian Lepas','paruh_waktu'=>'Paruh Waktu'] as $val=>$lbl)
          <option value="{{ $val }}" {{ old('employment_type', $employee->employment_type ?? 'tetap') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Status *</label>
        <select name="status" required>
          @foreach(['active'=>'Aktif','inactive'=>'Non-aktif','resigned'=>'Resign'] as $val=>$lbl)
          <option value="{{ $val }}" {{ old('status', $employee->status ?? 'active') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Tanggal Bergabung *</label>
        <input type="date" name="join_date" value="{{ old('join_date', isset($employee) ? $employee->join_date->toDateString() : '') }}" required>
      </div>
      <div class="form-group">
        <label>Tanggal Berhenti (jika ada)</label>
        <input type="date" name="end_date" value="{{ old('end_date', $isEdit ? $employee->end_date?->toDateString() : '') }}">
      </div>
      <div class="form-group">
        <label>No. KTP (NIK)</label>
        <input type="text" name="nik" value="{{ old('nik', $employee->nik ?? '') }}">
      </div>
      <div class="form-group">
        <label>NPWP</label>
        <input type="text" name="npwp" value="{{ old('npwp', $employee->npwp ?? '') }}">
      </div>
      <div class="form-group">
        <label>No. HP</label>
        <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}">
      </div>
      <div class="form-group full">
        <label>Alamat</label>
        <textarea name="address">{{ old('address', $employee->address ?? '') }}</textarea>
      </div>
    </div>

    <div class="card-title" style="margin-top:1.5rem">Komponen Gaji</div>
    <div class="form-grid">
      <div class="form-group">
        <label>Gaji Pokok (Rp) *</label>
        <input type="number" name="base_salary" value="{{ old('base_salary', $employee->base_salary ?? 0) }}" min="0" step="1000" required>
      </div>
      <div class="form-group">
        <label>Tunjangan Jabatan (Rp)</label>
        <input type="number" name="allowance_position" value="{{ old('allowance_position', $employee->allowance_position ?? 0) }}" min="0" step="1000">
      </div>
      <div class="form-group">
        <label>Tunjangan Transport (Rp)</label>
        <input type="number" name="allowance_transport" value="{{ old('allowance_transport', $employee->allowance_transport ?? 0) }}" min="0" step="1000">
      </div>
      <div class="form-group">
        <label>Tunjangan Makan (Rp)</label>
        <input type="number" name="allowance_meal" value="{{ old('allowance_meal', $employee->allowance_meal ?? 0) }}" min="0" step="1000">
      </div>
      <div class="form-group">
        <label>Tunjangan Lain-lain (Rp)</label>
        <input type="number" name="allowance_other" value="{{ old('allowance_other', $employee->allowance_other ?? 0) }}" min="0" step="1000">
      </div>
      <div class="form-group">
        <label>Status PTKP (dasar hitung PPh21) *</label>
        <select name="ptkp_status" required>
          @foreach(\App\Models\Employee::ptkpOptions() as $opt)
          <option value="{{ $opt }}" {{ old('ptkp_status', $employee->ptkp_status ?? 'TK/0') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="card-title" style="margin-top:1.5rem">Rekening &amp; BPJS</div>
    <div class="form-grid">
      <div class="form-group">
        <label>Nama Bank</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name ?? '') }}">
      </div>
      <div class="form-group">
        <label>No. Rekening</label>
        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number ?? '') }}">
      </div>
      <div class="form-group">
        <label>Nama Pemilik Rekening</label>
        <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $employee->bank_account_holder ?? '') }}">
      </div>
      <div class="form-group">
        <label>No. BPJS Kesehatan</label>
        <input type="text" name="bpjs_kesehatan_no" value="{{ old('bpjs_kesehatan_no', $employee->bpjs_kesehatan_no ?? '') }}">
      </div>
      <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem">
        <label style="display:flex;align-items:center;gap:.4rem;margin:0">
          <input type="checkbox" name="bpjs_kesehatan_active" value="1" style="width:auto" {{ old('bpjs_kesehatan_active', $employee->bpjs_kesehatan_active ?? true) ? 'checked' : '' }}> Ikut BPJS Kesehatan (potong gaji otomatis)
        </label>
      </div>
      <div class="form-group">
        <label>No. BPJS Ketenagakerjaan</label>
        <input type="text" name="bpjs_tk_no" value="{{ old('bpjs_tk_no', $employee->bpjs_tk_no ?? '') }}">
      </div>
      <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem">
        <label style="display:flex;align-items:center;gap:.4rem;margin:0">
          <input type="checkbox" name="bpjs_tk_active" value="1" style="width:auto" {{ old('bpjs_tk_active', $employee->bpjs_tk_active ?? true) ? 'checked' : '' }}> Ikut BPJS Ketenagakerjaan (potong gaji otomatis)
        </label>
      </div>
      <div class="form-group full">
        <label>Catatan</label>
        <textarea name="notes">{{ old('notes', $employee->notes ?? '') }}</textarea>
      </div>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:1rem">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan' : 'Tambah Karyawan' }}</button>
      <a href="{{ route('employees.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
@endsection
