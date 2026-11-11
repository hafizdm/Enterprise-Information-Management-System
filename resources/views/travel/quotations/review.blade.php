@extends('layouts.app')

@section('title', 'Review Penawaran Vendor ' . $inquiry->inquiry_number)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('travel.requests.show', $inquiry->travelRequest) }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Permintaan Perjalanan
            </a>
            <h1 class="h3 mt-2 text-gray-800">Evaluasi & Pemilihan Penawaran Rekanan</h1>
            <p class="text-muted small mb-0">Inquiry <strong>{{ $inquiry->inquiry_number }}</strong> dari vendor <strong>{{ $inquiry->vendor->name }}</strong> untuk permintaan <strong>{{ $inquiry->travelRequest->request_number }}</strong>.</p>
        </div>
        <div>
            @can('createQuotation', $inquiry)
                <a href="{{ route('travel.quotations.create', $inquiry) }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle me-1"></i> Input Opsi Penawaran Lain
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        @forelse($inquiry->quotations as $package)
            <div class="col-lg-6">
                <div class="card shadow-sm h-100 border {{ $package->status === 'selected' ? 'border-success border-2' : '' }}">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0 fw-bold">{{ $package->package_name }}</h5>
                            <div class="small text-muted">{{ $package->quotation_number }}</div>
                        </div>
                        <div>
                            @php
                                $statusClass = [
                                    'submitted' => 'bg-info text-dark',
                                    'selected' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'expired' => 'bg-secondary',
                                ][$package->status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ strtoupper($package->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="text-muted small">Total Biaya Penawaran:</span>
                            <div class="fs-4 fw-bold text-primary">Rp {{ number_format($package->total_estimated_cost, 0, ',', '.') }}</div>
                            <div class="small text-muted">Masa Berlaku: {{ $package->valid_until ? $package->valid_until->format('d/m/Y H:i') : '-' }}</div>
                        </div>

                        <h6>Rincian Layanan Termasuk:</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light small">
                                    <tr>
                                        <th>Layanan</th>
                                        <th>Detail</th>
                                        <th>Tanggal</th>
                                        <th class="text-end">Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($package->services as $svc)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ strtoupper($svc->service_type) }}</span>
                                                <div class="fw-bold small">{{ $svc->service_name }}</div>
                                            </td>
                                            <td class="small">{{ $svc->service_details ?: '-' }}</td>
                                            <td class="small">{{ $svc->service_date ? $svc->service_date->format('d/m/Y') : '-' }}</td>
                                            <td class="text-end small fw-bold">Rp {{ number_format($svc->estimated_cost, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($package->vendor_notes)
                            <div class="bg-light p-2 rounded small mb-3">
                                <strong>Catatan Vendor:</strong> {{ $package->vendor_notes }}
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                        @if($package->status === 'submitted')
                            @can('select', $package)
                                <form method="POST" action="{{ route('travel.quotations.select', $package) }}" onsubmit="return confirm('Pilih paket ini dan konfirmasi booking?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle me-1"></i> Pilih Opsi Ini & Konfirmasi Booking
                                    </button>
                                </form>
                            @endcan
                        @elseif($package->status === 'selected')
                            <div class="alert alert-success small mb-0 text-center">
                                <i class="bi bi-check-circle-fill me-1"></i> Opsi ini telah dipilih dan dikonfirmasi ke rekanan vendor.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada paket penawaran yang dikirimkan oleh vendor rekanan.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
