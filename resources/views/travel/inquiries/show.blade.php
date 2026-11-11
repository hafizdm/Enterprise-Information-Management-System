@extends('layouts.app')

@section('title', 'Inquiry Vendor ' . $inquiry->inquiry_number)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('travel.requests.show', $inquiry->travelRequest) }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Permintaan Perjalanan
            </a>
            <h1 class="h3 mt-2 text-gray-800">{{ $inquiry->inquiry_number }}</h1>
            <p class="text-muted small mb-0">Dikirim ke <strong>{{ $inquiry->vendor->name }}</strong> pada {{ $inquiry->sent_at ? $inquiry->sent_at->format('d/m/Y H:i') : '-' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @php
                $statusClass = [
                    'sent' => 'bg-warning text-dark',
                    'quoted' => 'bg-info text-dark',
                    'expired' => 'bg-danger',
                    'cancelled' => 'bg-secondary',
                ][$inquiry->status] ?? 'bg-secondary';
            @endphp
            <span class="badge {{ $statusClass }} fs-6">
                {{ strtoupper($inquiry->status) }}
            </span>
            @can('createQuotation', $inquiry)
                <a href="{{ route('travel.quotations.create', $inquiry) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Input Penawaran Vendor
                </a>
            @endcan
            @if($inquiry->quotations->count() > 0)
                <a href="{{ route('travel.quotations.review', $inquiry) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-check-circle me-1"></i> Evaluasi & Pilih
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Informasi Permintaan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small">No. Permintaan</label>
                            <div><strong>{{ $inquiry->travelRequest->request_number }}</strong></div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Proyek</label>
                            <div><strong>{{ $inquiry->travelRequest->project ? $inquiry->travelRequest->project->name : 'General' }}</strong></div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Rute & Tanggal</label>
                            <div class="fs-6 fw-bold text-primary">{{ $inquiry->travelRequest->origin }} &rarr; {{ $inquiry->travelRequest->destination }}</div>
                            <div class="small">
                                Berangkat: {{ $inquiry->travelRequest->departure_date ? $inquiry->travelRequest->departure_date->format('d/m/Y') : '-' }}
                                @if($inquiry->travelRequest->return_date)
                                    | Kembali: {{ $inquiry->travelRequest->return_date->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                        @if($inquiry->deadline_at)
                            <div class="col-12">
                                <label class="text-muted small">Batas Waktu Vendor (Deadline)</label>
                                <div class="text-danger fw-bold"><i class="bi bi-clock me-1"></i> {{ $inquiry->deadline_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                        @if($inquiry->notes)
                            <div class="col-12">
                                <label class="text-muted small">Catatan untuk Vendor</label>
                                <div class="bg-light p-2 rounded small">{{ $inquiry->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Daftar Penumpang</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($inquiry->travelRequest->passengers as $p)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $p->passenger_name }}</strong>
                                    @if($p->id_card_number)
                                        <div class="small text-muted">ID: {{ $p->id_card_number }}</div>
                                    @endif
                                </div>
                                <span class="badge bg-light text-dark border">{{ $p->gender ? ucfirst($p->gender) : '-' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quotations section -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Opsi Penawaran dari Vendor (Quotations)</h5>
                </div>
                <div class="card-body">
                    @if($inquiry->quotations->count() > 0)
                        <div class="row g-3">
                            @foreach($inquiry->quotations as $quotation)
                                <div class="col-md-6">
                                    <div class="card border {{ $quotation->status === 'selected' ? 'border-success' : '' }}">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold">{{ $quotation->package_name }}</h6>
                                            <span class="badge {{ $quotation->status === 'selected' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ strtoupper($quotation->status) }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="text-primary fw-bold">Rp {{ number_format($quotation->total_estimated_cost, 0, ',', '.') }}</h5>
                                            <div class="small text-muted mb-3">Berlaku s/d: {{ $quotation->valid_until ? $quotation->valid_until->format('d/m/Y H:i') : '-' }}</div>
                                            <ul class="list-unstyled small mb-0">
                                                @foreach($quotation->services as $svc)
                                                    <li class="mb-1">
                                                        <i class="bi bi-chevron-right text-muted"></i>
                                                        <strong>{{ strtoupper($svc->service_type) }}:</strong> {{ $svc->service_name }} (Rp {{ number_format($svc->estimated_cost, 0, ',', '.') }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-hourglass-split fs-2 d-block mb-2"></i>
                            Menunggu penawaran harga dari vendor rekanan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
