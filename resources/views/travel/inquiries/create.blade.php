@extends('layouts.app')

@section('title', 'Kirim Inquiry ke Rekanan Vendor')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('travel.requests.show', $travelRequest) }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Permintaan
        </a>
        <h1 class="h3 mt-2 text-gray-800">Kirim Inquiry ke Rekanan Vendor</h1>
        <p class="text-muted small">Kirim permintaan penawaran harga perjalanan ke rekanan vendor aktif.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Ringkasan Permintaan Perjalanan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">No. Permintaan</label>
                            <div class="fw-bold">{{ $travelRequest->request_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Proyek</label>
                            <div class="fw-bold">{{ $travelRequest->project ? $travelRequest->project->name : 'General' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Rute</label>
                            <div class="fw-bold text-primary">{{ $travelRequest->origin }} &rarr; {{ $travelRequest->destination }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Jadwal</label>
                            <div>Berangkat: <strong>{{ $travelRequest->departure_date ? $travelRequest->departure_date->format('d/m/Y') : '-' }}</strong></div>
                            @if($travelRequest->return_date)
                                <div>Kembali: <strong>{{ $travelRequest->return_date->format('d/m/Y') }}</strong></div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Layanan yang Dibutuhkan</label>
                            <div class="d-flex gap-2">
                                @if($travelRequest->need_flight) <span class="badge bg-primary"><i class="bi bi-airplane"></i> Pesawat</span> @endif
                                @if($travelRequest->need_hotel) <span class="badge bg-success"><i class="bi bi-building"></i> Hotel</span> @endif
                                @if($travelRequest->need_local_transport) <span class="badge bg-warning text-dark"><i class="bi bi-car-front"></i> Transportasi</span> @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Daftar Penumpang ({{ $travelRequest->passengers->count() }} orang)</label>
                            <ul class="list-group list-group-flush border rounded">
                                @foreach($travelRequest->passengers as $p)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $p->passenger_name }}</strong>
                                            @if($p->id_card_number)
                                                <span class="text-muted small">({{ $p->id_card_number }})</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ $p->gender ? ucfirst($p->gender) : '-' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Rekanan Vendor Aktif</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>{{ $activeVendor->name }}</strong>
                            <div class="small text-muted">Kode: {{ $activeVendor->code }} | Kontak: {{ $activeVendor->pic_name ?? '-' }} ({{ $activeVendor->pic_phone ?? '-' }})</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('travel.inquiries.store') }}">
                        @csrf
                        <input type="hidden" name="travel_request_id" value="{{ $travelRequest->id }}">
                        <input type="hidden" name="vendor_id" value="{{ $activeVendor->id }}">

                        <div class="mb-3">
                            <label class="form-label">Batas Waktu Penawaran (Deadline)</label>
                            <input type="datetime-local" name="deadline_at" class="form-control" value="{{ old('deadline_at', now()->addHours(6)->format('Y-m-d\TH:i')) }}">
                            <div class="form-text small">Vendor diharapkan mengirim penawaran harga sebelum batas waktu ini.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan Khusus untuk Vendor</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Instruksi rute alternatif, maskapai yang diinginkan, atau batasan bagasi...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Kirim Inquiry Sekarang
                            </button>
                            <a href="{{ route('travel.requests.show', $travelRequest) }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
