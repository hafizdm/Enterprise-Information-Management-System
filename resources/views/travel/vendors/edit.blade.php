@extends('layouts.app')

@section('title', 'Edit Vendor Travel - EIMS')

@section('content')

<div class="mb-4">
    <a href="{{ route('travel.vendors.show', $vendor) }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Vendor
    </a>
    <h4 class="mb-1">Edit Vendor: {{ $vendor->name }}</h4>
    <p class="text-muted mb-0">Perbarui data profil dan kontak rekanan travel</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('travel.vendors.update', $vendor) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kode Rekanan <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $vendor->code) }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nama Biro Perjalanan / Travel Agency <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $vendor->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Contact Person (PIC)</label>
                    <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $vendor->contact_person) }}">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $vendor->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email Operasional</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $vendor->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Alamat Kantor</label>
                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $vendor->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-3 text-muted">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bank me-1"></i> Informasi Rekening Bank Rekanan</h6>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Bank</label>
                    <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $vendor->bank_name) }}">
                    @error('bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror" value="{{ old('bank_account_number', $vendor->bank_account_number) }}">
                    @error('bank_account_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror" value="{{ old('bank_account_holder', $vendor->bank_account_holder) }}">
                    @error('bank_account_holder')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Catatan Internal</label>
                    <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $vendor->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('travel.vendors.show', $vendor) }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
