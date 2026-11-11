@extends('layouts.app')

@section('title', 'Daftar Booking Terkonfirmasi')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Daftar Booking Perjalanan (Confirmed Bookings)</h1>
            <p class="text-muted small mb-0">Booking yang telah dikonfirmasi dan menunggu issued tiket dari rekanan vendor.</p>
        </div>
        <div>
            <a href="{{ route('travel.requests.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Antrean Travel
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Booking</th>
                            <th>No. Permintaan</th>
                            <th>Vendor Rekanan</th>
                            <th>Rute Perjalanan</th>
                            <th>Penumpang</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                            <th>Tiket Terunggah</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->booking_number }}</strong>
                                    <div class="small text-muted">{{ $booking->confirmed_at ? $booking->confirmed_at->format('d/m/Y H:i') : '-' }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('travel.requests.show', $booking->travelRequest) }}" class="text-decoration-none">
                                        {{ $booking->travelRequest->request_number }}
                                    </a>
                                </td>
                                <td>{{ $booking->vendor->name }}</td>
                                <td>{{ $booking->travelRequest->origin }} &rarr; {{ $booking->travelRequest->destination }}</td>
                                <td>{{ $booking->travelRequest->passengers->count() }} orang</td>
                                <td class="fw-bold text-primary">Rp {{ number_format($booking->total_actual_amount, 0, ',', '.') }}</td>
                                <td>
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
                                </td>
                                <td>
                                    @if($booking->tickets->count() > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-file-earmark-check"></i> {{ $booking->tickets->count() }} berkas
                                        </span>
                                    @else
                                        <span class="text-muted small">Menunggu vendor</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('travel.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                        Detail & Verifikasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Belum ada booking perjalanan terkonfirmasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bookings->hasPages())
            <div class="card-footer bg-white">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
