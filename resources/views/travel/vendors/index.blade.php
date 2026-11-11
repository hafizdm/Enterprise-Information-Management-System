@extends('layouts.app')

@section('title', 'Master Vendor Travel - EIMS')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Vendor Travel</h4>
        <p class="text-muted mb-0">
            Kelola master rekanan biro perjalanan (Travel Agency) & vendor aktif
        </p>
    </div>

    @can('travel.vendor-manage')
    <a href="{{ route('travel.vendors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Vendor
    </a>
    @endcan
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

<!-- Active Vendor Banner -->
@if($activeVendor)
    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-success">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-success-subtle text-success px-2 py-1 mb-1">
                    <i class="bi bi-check-circle-fill me-1"></i> Rekanan Aktif Saat Ini
                </span>
                <h5 class="mb-1 fw-bold text-dark">{{ $activeVendor->name }} ({{ $activeVendor->code }})</h5>
                <p class="text-muted mb-0 small">
                    <i class="bi bi-person me-1"></i> Kontak: {{ $activeVendor->contact_person ?? '-' }} |
                    <i class="bi bi-telephone me-1"></i> Telepon: {{ $activeVendor->phone ?? '-' }} |
                    <i class="bi bi-envelope me-1"></i> Email: {{ $activeVendor->email ?? '-' }}
                </p>
            </div>
            <a href="{{ route('travel.vendors.show', $activeVendor) }}" class="btn btn-outline-success btn-sm">
                Lihat Detail Rekanan
            </a>
        </div>
    </div>
@else
    <div class="alert alert-warning mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i>
        <strong>Perhatian:</strong> Belum ada vendor yang diaktifkan sebagai rekanan utama untuk pemesanan tiket baru.
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Search & Filter -->
        <form method="GET" action="{{ route('travel.vendors.index') }}" class="row g-2 mb-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kode, nama, kontak, telepon, email..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
            @if($search || $status !== null && $status !== '')
                <div class="col-md-2">
                    <a href="{{ route('travel.vendors.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">#</th>
                        <th>Kode</th>
                        <th>Nama Vendor</th>
                        <th>Kontak & Telepon</th>
                        <th>Status</th>
                        <th>Akun Portal</th>
                        <th width="160" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $loop->iteration + ($vendors->currentPage() - 1) * $vendors->perPage() }}</td>
                            <td><span class="badge bg-light text-dark font-monospace">{{ $vendor->code }}</span></td>
                            <td>
                                <a href="{{ route('travel.vendors.show', $vendor) }}" class="fw-semibold text-decoration-none">
                                    {{ $vendor->name }}
                                </a>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $vendor->contact_person ?? '-' }}</div>
                                <div class="text-muted small">{{ $vendor->phone ?? '-' }}</div>
                            </td>
                            <td>
                                @if($vendor->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    <i class="bi bi-people me-1"></i> {{ $vendor->users_count }} Akun
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('travel.vendors.show', $vendor) }}" class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('travel.vendor-manage')
                                    <a href="{{ route('travel.vendors.edit', $vendor) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-building-x fs-3 d-block mb-2"></i>
                                Belum ada data vendor travel yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div class="mt-3">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
