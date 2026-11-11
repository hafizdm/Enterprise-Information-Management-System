@extends('layouts.app')

@section('title', 'Buat Permintaan Perjalanan Proyek')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('travel.requests.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Kembali ke Antrean
        </a>
        <h1 class="h3 mt-2 text-gray-800">Buat Permintaan Perjalanan Proyek (Project Travel Request)</h1>
        <p class="text-muted small">Formulir pengajuan perjalanan dinas lapangan / tim proyek untuk satu atau banyak penumpang.</p>
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

    <form method="POST" action="{{ route('travel.requests.store') }}" id="projectTravelForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">1. Data Perjalanan & Rute</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Proyek</label>
                                <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Proyek --</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->cost_center ?? '' }} - {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Kategori Perjalanan</label>
                                <select name="travel_category" class="form-select" required>
                                    <option value="domestic" {{ old('travel_category') === 'domestic' ? 'selected' : '' }}>Domestik</option>
                                    <option value="international" {{ old('travel_category') === 'international' ? 'selected' : '' }}>Internasional</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Kota Asal (Origin)</label>
                                <input type="text" name="origin" class="form-control @error('origin') is-invalid @enderror" value="{{ old('origin') }}" placeholder="Contoh: Jakarta (CGK)" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Kota Tujuan (Destination)</label>
                                <input type="text" name="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination') }}" placeholder="Contoh: Balikpapan (BPN)" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Tanggal Berangkat</label>
                                <input type="date" name="departure_date" class="form-control @error('departure_date') is-invalid @enderror" value="{{ old('departure_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Berangkat Pilihan</label>
                                <input type="text" name="departure_time_window" class="form-control" value="{{ old('departure_time_window') }}" placeholder="Misal: Pagi 07:00 - 10:00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Kembali (Opsional)</label>
                                <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Kembali Pilihan</label>
                                <input type="text" name="return_time_window" class="form-control" value="{{ old('return_time_window') }}" placeholder="Misal: Sore 16:00 - 19:00">
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block">Kebutuhan Layanan Perjalanan:</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="need_flight" id="needFlight" value="1" {{ old('need_flight', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="needFlight">Tiket Pesawat</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="need_hotel" id="needHotel" value="1" {{ old('need_hotel') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="needHotel">Hotel / Akomodasi</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="need_local_transport" id="needTransport" value="1" {{ old('need_local_transport') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="needTransport">Transportasi Lokal / Rental</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan khusus, preferensi maskapai/hotel, atau keperluan proyek...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">2. Daftar Penumpang (Multi-Passenger — Pengisian Manual)</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addPassengerBtn">
                            <i class="bi bi-plus-lg"></i> Tambah Penumpang
                        </button>
                    </div>
                    <div class="card-body" id="passengerList">
                        <div class="passenger-item border rounded p-3 mb-3 bg-light" data-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-primary passenger-title">Penumpang #1</h6>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small required">Nama Lengkap (Sesuai KTP/Paspor)</label>
                                    <input type="text" name="passengers[0][passenger_name]" class="form-control form-control-sm" required placeholder="Nama lengkap penumpang">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small required">Jenis Identitas</label>
                                    <select name="passengers[0][id_card_type]" class="form-select form-select-sm" required>
                                        <option value="ktp">KTP</option>
                                        <option value="passport">Paspor</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Jenis Kelamin</label>
                                    <select name="passengers[0][gender]" class="form-select form-select-sm">
                                        <option value="">-- Pilih --</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Nomor Identitas (NIK/Paspor)</label>
                                    <input type="text" name="passengers[0][id_card_number]" class="form-control form-control-sm" placeholder="Nomor KTP atau Paspor">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Nomor Telepon / WhatsApp</label>
                                    <input type="text" name="passengers[0][phone_number]" class="form-control form-control-sm" placeholder="Nomor kontak aktif">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Kebutuhan Khusus (Opsional)</label>
                                    <input type="text" name="passengers[0][specific_needs]" class="form-control form-control-sm" placeholder="Misal: Bagasi ekstra, kursi roda">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Informasi Pengajuan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Tanggal Pengajuan</label>
                            <input type="date" name="request_date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                        <div class="alert alert-info small mb-4">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Setelah disimpan, permintaan ini akan masuk ke <strong>Antrean Travel Terpusat</strong> untuk diproses inquiry ke rekanan vendor aktif oleh HR/GA Head Office (HRD).
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Simpan & Kirim Permintaan
                            </button>
                            <a href="{{ route('travel.requests.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let passengerCount = 1;
    const addBtn = document.getElementById('addPassengerBtn');
    const container = document.getElementById('passengerList');

    addBtn.addEventListener('click', function () {
        const index = passengerCount++;
        const itemHtml = `
            <div class="passenger-item border rounded p-3 mb-3 bg-light" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-primary passenger-title">Penumpang #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-passenger-btn">Hapus</button>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small required">Nama Lengkap (Sesuai KTP/Paspor)</label>
                        <input type="text" name="passengers[${index}][passenger_name]" class="form-control form-control-sm" required placeholder="Nama lengkap penumpang">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small required">Jenis Identitas</label>
                        <select name="passengers[${index}][id_card_type]" class="form-select form-select-sm" required>
                            <option value="ktp">KTP</option>
                            <option value="passport">Paspor</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Jenis Kelamin</label>
                        <select name="passengers[${index}][gender]" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Nomor Identitas (NIK/Paspor)</label>
                        <input type="text" name="passengers[${index}][id_card_number]" class="form-control form-control-sm" placeholder="Nomor KTP atau Paspor">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="passengers[${index}][phone_number]" class="form-control form-control-sm" placeholder="Nomor kontak aktif">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Kebutuhan Khusus (Opsional)</label>
                        <input type="text" name="passengers[${index}][specific_needs]" class="form-control form-control-sm" placeholder="Misal: Bagasi ekstra, kursi roda">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-passenger-btn')) {
            e.target.closest('.passenger-item').remove();
            const items = container.querySelectorAll('.passenger-item');
            items.forEach((item, idx) => {
                item.querySelector('.passenger-title').innerText = `Penumpang #${idx + 1}`;
            });
        }
    });
});
</script>
@endsection
