@extends('layouts.app')

@section('title', 'Detail Vendor - ' . $vendor->name . ' - EIMS')

@section('content')

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('travel.vendors.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Vendor
        </a>
        <h4 class="mb-1">{{ $vendor->name }}</h4>
        <p class="text-muted mb-0 small">Kode Rekanan: <span class="badge bg-light text-dark font-monospace">{{ $vendor->code }}</span></p>
    </div>

    <div class="d-flex gap-2">
        @can('travel.vendor-manage')
            @if(!$vendor->is_active)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#activateModal">
                    <i class="bi bi-check-circle me-1"></i> Aktifkan Rekanan Utama
                </button>
            @else
                <span class="badge bg-success-subtle text-success border border-success d-flex align-items-center px-3 py-2 fs-6">
                    <i class="bi bi-check-circle-fill me-2"></i> Rekanan Utama Aktif
                </span>
            @endif

            <a href="{{ route('travel.vendors.edit', $vendor) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit Data
            </a>
        @endcan
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Profil & Kontak -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-building me-1"></i> Informasi Rekanan</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td width="180" class="text-muted">Nama Rekanan</td>
                            <td class="fw-semibold">: {{ $vendor->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Booking</td>
                            <td>: 
                                @if($vendor->is_active)
                                    <span class="badge bg-success">Aktif (Menerima Booking Baru)</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Contact Person (PIC)</td>
                            <td>: {{ $vendor->contact_person ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Telepon</td>
                            <td>: {{ $vendor->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email Operasional</td>
                            <td>: {{ $vendor->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat Kantor</td>
                            <td>: {{ $vendor->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td>: {{ $vendor->notes ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Informasi Rekening Bank -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-bank me-1"></i> Rekening Bank Pembayaran</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td width="150" class="text-muted">Nama Bank</td>
                            <td class="fw-semibold">: {{ $vendor->bank_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Rekening</td>
                            <td class="fw-semibold font-monospace">: {{ $vendor->bank_account_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Atas Nama</td>
                            <td>: {{ $vendor->bank_account_holder ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Saluran Komunikasi Vendor -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-envelope-at me-1"></i> Saluran Komunikasi Resmi Vendor
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-center mb-0">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                Mitra rekanan vendor <strong>tidak memiliki akun login EIMS</strong>. Seluruh proses pengiriman inquiry, penerimaan penawaran (quotation), dan penerbitan tiket/voucher dilakukan melalui komunikasi email resmi ke:
                <strong class="d-block mt-1">{{ $vendor->email ?? 'Email belum diatur' }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Aktivasi Vendor -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-1"></i> Riwayat Status Aktivasi Rekanan</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Tanggal Efektif</th>
                        <th>Tanggal Nonaktif</th>
                        <th>Petugas (Aktor)</th>
                        <th>Alasan & Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendor->activeHistories as $h)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $h->effective_date?->format('d M Y') }}</td>
                            <td>
                                @if($h->deactivated_date)
                                    {{ $h->deactivated_date->format('d M Y') }}
                                @else
                                    <span class="badge bg-success-subtle text-success">Sedang Berlangsung</span>
                                @endif
                            </td>
                            <td>{{ $h->changedByUser?->username ?? '-' }}</td>
                            <td>{{ $h->reason }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat aktivasi rekanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@can('travel.vendor-manage')
@if(!$vendor->is_active && $vendor->users->count() === 0)
    <div class="card border-danger-subtle bg-danger-subtle bg-opacity-10 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-danger mb-1">Hapus Data Rekanan</h6>
                <p class="text-muted small mb-0">Vendor yang tidak aktif dan tidak memiliki akun pengguna portal dapat dihapus dari master data.</p>
            </div>
            <form method="POST" action="{{ route('travel.vendors.destroy', $vendor) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data vendor ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Hapus Vendor
                </button>
            </form>
        </div>
    </div>
@endif

<!-- Modal Aktivasi Vendor -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('travel.vendors.activate', $vendor) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Aktivasi Rekanan Utama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">
                        Mengaktifkan vendor <strong>{{ $vendor->name }}</strong> akan secara otomatis menonaktifkan vendor aktif sebelumnya.
                        Hanya ada <strong>satu vendor aktif</strong> yang dapat menerima proses pemesanan tiket baru.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Efektif <span class="text-danger">*</span></label>
                        <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Penetapan / Perubahan Rekanan <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="Jelaskan alasan penunjukan vendor aktif ini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Konfirmasi Aktivasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection
