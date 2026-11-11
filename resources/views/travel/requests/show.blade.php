@extends('layouts.app')

@section('title', 'Detail Permintaan Perjalanan ' . $travelRequest->request_number)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('travel.requests.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Antrean
            </a>
            <div class="d-flex align-items-center gap-2 mt-1">
                <h1 class="h3 mb-0 text-gray-800">{{ $travelRequest->request_number }}</h1>
                @php
                    $statusClass = [
                        'pending' => 'bg-warning text-dark',
                        'in_inquiry' => 'bg-primary',
                        'quoted' => 'bg-info text-dark',
                        'approved' => 'bg-success',
                        'booked' => 'bg-primary',
                        'ticket_issued' => 'bg-success',
                        'completed' => 'bg-secondary',
                        'cancelled' => 'bg-danger',
                    ][$travelRequest->status] ?? 'bg-secondary';
                @endphp
                <span class="badge {{ $statusClass }}">
                    {{ strtoupper(str_replace('_', ' ', $travelRequest->status)) }}
                </span>
            </div>
            <p class="text-muted small mb-0">Diajukan pada {{ $travelRequest->request_date ? $travelRequest->request_date->format('d F Y') : '-' }} oleh {{ $travelRequest->creator->username ?? '-' }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($travelRequest->status === 'pending')
                @can('travel.inquiry')
                    <a href="{{ route('travel.inquiries.create', ['travel_request_id' => $travelRequest->id]) }}" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Buat Inquiry ke Vendor
                    </a>
                @endcan
                @can('cancel', $travelRequest)
                    <form method="POST" action="{{ route('travel.requests.cancel', $travelRequest) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i> Batalkan
                        </button>
                    </form>
                @endcan
            @endif
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

    <div class="row g-4">
        <!-- Main details -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Rincian Perjalanan & Rute</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Sumber Permintaan</label>
                            <div>
                                @if($travelRequest->source_type === 'spd')
                                    <span class="badge bg-info text-dark">Surat Perjalanan Dinas (SPD)</span>
                                    @if($travelRequest->spd)
                                        <div class="mt-1">
                                            No. SPD: <strong>{{ $travelRequest->spd->spd_number }}</strong>
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Pengajuan Langsung Proyek</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Proyek & Cost Center</label>
                            <div>
                                <strong>{{ $travelRequest->project ? ($travelRequest->project->cost_center ? $travelRequest->project->cost_center . ' - ' : '') . $travelRequest->project->name : 'General Project' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Rute</label>
                            <div class="fs-5 fw-bold text-primary">
                                {{ $travelRequest->origin }} &rarr; {{ $travelRequest->destination }}
                            </div>
                            <span class="badge bg-light text-dark border">{{ ucfirst($travelRequest->travel_category) }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Jadwal Keberangkatan & Kepulangan</label>
                            <div>
                                <strong>Berangkat:</strong> {{ $travelRequest->departure_date ? $travelRequest->departure_date->format('d/m/Y') : '-' }}
                                @if($travelRequest->departure_time_window)
                                    <span class="text-muted">({{ $travelRequest->departure_time_window }})</span>
                                @endif
                            </div>
                            @if($travelRequest->return_date)
                                <div>
                                    <strong>Kembali:</strong> {{ $travelRequest->return_date->format('d/m/Y') }}
                                    @if($travelRequest->return_time_window)
                                        <span class="text-muted">({{ $travelRequest->return_time_window }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Kebutuhan Layanan</label>
                            <div class="d-flex gap-2">
                                @if($travelRequest->need_flight) <span class="badge bg-primary fs-6"><i class="bi bi-airplane"></i> Pesawat</span> @endif
                                @if($travelRequest->need_hotel) <span class="badge bg-success fs-6"><i class="bi bi-building"></i> Hotel</span> @endif
                                @if($travelRequest->need_local_transport) <span class="badge bg-warning text-dark fs-6"><i class="bi bi-car-front"></i> Transportasi</span> @endif
                            </div>
                        </div>
                        @if($travelRequest->notes)
                            <div class="col-12">
                                <label class="text-muted small">Catatan</label>
                                <div class="bg-light p-3 rounded">
                                    {{ nl2br(e($travelRequest->notes)) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Daftar Penumpang ({{ $travelRequest->passengers->count() }} orang)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Penumpang</th>
                                    <th>No. KTP / Paspor</th>
                                    <th>Gender</th>
                                    <th>No. HP</th>
                                    <th>Kebutuhan Khusus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($travelRequest->passengers as $idx => $passenger)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $passenger->passenger_name }}</strong>
                                        </td>
                                        <td>
                                            @if($passenger->id_card_number)
                                                <span class="badge bg-light text-dark border">{{ strtoupper($passenger->id_card_type ?? 'KTP') }}</span>
                                                {{ $passenger->id_card_number }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $passenger->gender ? ucfirst($passenger->gender) : '-' }}</td>
                                        <td>{{ $passenger->phone_number ?: '-' }}</td>
                                        <td>{{ $passenger->specific_needs ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Belum ada penumpang terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar progress & actions -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Tahapan Alur Perjalanan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>1. Permintaan Masuk Antrean</span>
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>2. Inquiry ke Rekanan Vendor</span>
                            @if(in_array($travelRequest->status, ['in_inquiry', 'quoted', 'approved', 'booked', 'ticket_issued', 'completed']))
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-circle text-muted"></i>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>3. Penawaran Vendor (Quotation)</span>
                            @if(in_array($travelRequest->status, ['quoted', 'approved', 'booked', 'ticket_issued', 'completed']))
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-circle text-muted"></i>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>4. Pemilihan Opsi / Approval</span>
                            @if(in_array($travelRequest->status, ['approved', 'booked', 'ticket_issued', 'completed']))
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-circle text-muted"></i>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>5. Final Booking & E-Ticket</span>
                            @if(in_array($travelRequest->status, ['booked', 'ticket_issued', 'completed']))
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-circle text-muted"></i>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Inquiries & Bookings quick info -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Riwayat Terkait</h5>
                </div>
                <div class="card-body">
                    <h6>Inquiry Vendor</h6>
                    @if($travelRequest->inquiries->count() > 0)
                        <ul class="list-unstyled small mb-3">
                            @foreach($travelRequest->inquiries as $inq)
                                <li class="mb-2 pb-2 border-bottom">
                                    <div><strong>{{ $inq->inquiry_number }}</strong> ({{ $inq->vendor->name ?? 'Vendor' }})</div>
                                    <div class="text-muted">Status: {{ strtoupper($inq->status) }} | Penawaran: {{ $inq->quotations->count() }} opsi</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="small text-muted mb-3">Belum ada inquiry dikirim ke vendor.</p>
                    @endif

                    <h6>Booking & E-Ticket</h6>
                    @if($travelRequest->bookings->count() > 0)
                        <ul class="list-unstyled small mb-0">
                            @foreach($travelRequest->bookings as $bkg)
                                <li class="mb-2 pb-2 border-bottom">
                                    <div><strong>{{ $bkg->booking_number }}</strong></div>
                                    <div class="text-muted">Status: {{ strtoupper($bkg->status) }} | Tiket: {{ $bkg->tickets->count() }} berkas</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="small text-muted mb-0">Belum ada booking konfirmasi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
