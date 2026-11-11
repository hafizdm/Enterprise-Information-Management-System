@extends('layouts.app')

@section('title', 'Detail Booking ' . $booking->booking_number)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('travel.bookings.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Booking
            </a>
            <div class="d-flex align-items-center gap-2 mt-1">
                <h1 class="h3 mb-0 text-gray-800">{{ $booking->booking_number }}</h1>
                @php
                    $statusClass = [
                        'confirmed' => 'bg-warning text-dark',
                        'ticket_issued' => 'bg-info text-dark',
                        'verified' => 'bg-success',
                        'completed' => 'bg-secondary',
                        'cancelled' => 'bg-danger',
                    ][$booking->status] ?? 'bg-secondary';
                @endphp
                <span class="badge {{ $statusClass }}">
                    {{ strtoupper(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>
            <p class="text-muted small mb-0">Dikonfirmasi ke <strong>{{ $booking->vendor->name }}</strong> pada {{ $booking->confirmed_at ? $booking->confirmed_at->format('d/m/Y H:i') : '-' }}</p>
        </div>
        <div>
            <a href="{{ route('travel.requests.show', $booking->travelRequest) }}" class="btn btn-outline-secondary">
                Lihat Permintaan {{ $booking->travelRequest->request_number }}
            </a>
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
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Rincian Perjalanan & Rekanan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small">Vendor Rekanan</label>
                            <div class="fw-bold">{{ $booking->vendor->name }}</div>
                            <div class="small text-muted">{{ $booking->vendor->email }}</div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Total Biaya Aktual</label>
                            <div class="fs-5 fw-bold text-primary">Rp {{ number_format($booking->total_actual_amount, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Rute & Jadwal</label>
                            <div class="fs-6 fw-bold text-dark">{{ $booking->travelRequest->origin }} &rarr; {{ $booking->travelRequest->destination }}</div>
                            <div class="small text-muted">
                                Berangkat: {{ $booking->travelRequest->departure_date ? $booking->travelRequest->departure_date->format('d/m/Y') : '-' }}
                                @if($booking->travelRequest->return_date)
                                    | Kembali: {{ $booking->travelRequest->return_date->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Layanan Dipesan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Jenis</th>
                                    <th>Nama Layanan</th>
                                    <th>Kode Booking / PNR</th>
                                    <th class="text-end">Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->services as $svc)
                                    <tr>
                                        <td><span class="badge bg-light text-dark border">{{ strtoupper($svc->service_type) }}</span></td>
                                        <td><strong>{{ $svc->service_name }}</strong></td>
                                        <td>{{ $svc->booking_reference ?: '-' }}</td>
                                        <td class="text-end">Rp {{ number_format($svc->actual_cost, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets section -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Berkas Tiket / Voucher Terbit (E-Tickets & Vouchers)</h5>
                    <span class="badge bg-light text-dark border">{{ $booking->tickets->count() }} berkas tersimpan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Tiket / Kode</th>
                                    <th>Jenis Berkas</th>
                                    <th>Nama Berkas</th>
                                    <th>Diunggah Oleh</th>
                                    <th>Waktu Unggah</th>
                                    <th>Status Verifikasi</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($booking->tickets as $ticket)
                                    <tr>
                                        <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ strtoupper(str_replace('_', ' ', $ticket->ticket_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                            {{ $ticket->file_name }}
                                            @if($ticket->file_size)
                                                <span class="text-muted small">({{ round($ticket->file_size / 1024) }} KB)</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->uploadedBy->username ?? 'Vendor' }}</td>
                                        <td>{{ $ticket->uploaded_at ? $ticket->uploaded_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            @if($ticket->is_verified)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-patch-check-fill me-1"></i> Diverifikasi ({{ $ticket->verifiedBy->username ?? 'HR' }})
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i> Menunggu Verifikasi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('travel.tickets.download', $ticket) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Unduh Tiket">
                                                    <i class="bi bi-download"></i> Unduh
                                                </a>
                                                @if(!$ticket->is_verified)
                                                    @can('verify', $ticket)
                                                        <form method="POST" action="{{ route('travel.tickets.verify', $ticket) }}" onsubmit="return confirm('Verifikasi keabsahan tiket/voucher ini?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="bi bi-check-lg"></i> Verifikasi
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-hourglass-split fs-3 d-block mb-1"></i>
                                            Belum ada berkas tiket atau voucher yang diunggah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @can('upload', [\App\Models\Travel\TravelTicket::class, $booking])
        <div class="col-12 mt-2">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Unggah Berkas Tiket / Voucher dari Rekanan Vendor</h5>
                    <p class="text-muted small mb-0">Unggah berkas tiket elektronik atau voucher hotel yang diterima dari rekanan vendor via email.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('travel.bookings.tickets.store', $booking) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label required">Nomor Tiket / PNR / Kode Booking</label>
                                <input type="text" name="ticket_number" class="form-control" placeholder="Contoh: 126-1234567890 / PNR: ABCDEF" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Jenis Dokumen</label>
                                <select name="ticket_type" class="form-select" required>
                                    <option value="flight_eticket">E-Ticket Pesawat</option>
                                    <option value="hotel_voucher">Voucher Hotel</option>
                                    <option value="train_ticket">Tiket Kereta Api</option>
                                    <option value="other">Voucher / Dokumen Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Terkait Layanan</label>
                                <select name="travel_booking_service_id" class="form-select">
                                    <option value="">-- Semua / Layanan Umum --</option>
                                    @foreach($booking->services as $svc)
                                        <option value="{{ $svc->id }}">{{ strtoupper($svc->service_type) }} - {{ $svc->service_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Berkas Tiket / Voucher (PDF / JPG / PNG, Maks. 10MB)</label>
                                <input type="file" name="ticket_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="notes" class="form-control" placeholder="Catatan batas waktu, terminal keberangkatan, dsb.">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-1"></i> Simpan & Unggah Dokumen
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
