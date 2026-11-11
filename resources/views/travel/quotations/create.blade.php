@extends('layouts.app')

@section('title', 'Input Penawaran Harga Rekanan')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('travel.inquiries.show', $inquiry) }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Inquiry
        </a>
        <h1 class="h3 mt-2 text-gray-800">Input Penawaran Rekanan (Quotation)</h1>
        <p class="text-muted small">Input penawaran harga yang diterima dari rekanan vendor <strong>{{ $inquiry->vendor->name }}</strong> via email untuk inquiry <strong>{{ $inquiry->inquiry_number }}</strong>.</p>
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

    <form method="POST" action="{{ route('travel.quotations.store', $inquiry) }}">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">1. Data Paket Alternatif Penawaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label required">Nama Paket / Opsi</label>
                                <input type="text" name="package_name" class="form-control" placeholder="Contoh: Opsi A - Penerbangan Garuda Pagi" value="{{ old('package_name') }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label required">Masa Berlaku Penawaran (Valid Until)</label>
                                <input type="datetime-local" name="valid_until" class="form-control" value="{{ old('valid_until', now()->addHours(12)->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan Tambahan dari Vendor</label>
                                <textarea name="vendor_notes" class="form-control" rows="2" placeholder="Ketentuan refund, batas waktu issued tiket, bagasi termasuk...">{{ old('vendor_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">2. Rincian Layanan & Tarif (Services)</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addServiceBtn">
                            <i class="bi bi-plus-lg"></i> Tambah Layanan
                        </button>
                    </div>
                    <div class="card-body" id="serviceList">
                        <div class="service-item border rounded p-3 mb-3 bg-light" data-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-primary service-title">Layanan #1</h6>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small required">Jenis Layanan</label>
                                    <select name="services[0][service_type]" class="form-select form-select-sm" required>
                                        <option value="flight">Tiket Pesawat</option>
                                        <option value="hotel">Hotel / Penginapan</option>
                                        <option value="car_rental">Rental Mobil / Transport</option>
                                        <option value="train">Kereta Api</option>
                                        <option value="bus">Bus Travel</option>
                                        <option value="ferry">Kapal Laut</option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small required">Nama Layanan / Maskapai / Hotel</label>
                                    <input type="text" name="services[0][service_name]" class="form-control form-control-sm" placeholder="Contoh: Garuda GA-582 CGK - BPN" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small required">Tanggal Pelaksanaan</label>
                                    <input type="date" name="services[0][service_date]" class="form-control form-control-sm" value="{{ $inquiry->travelRequest->departure_date ? $inquiry->travelRequest->departure_date->format('Y-m-d') : date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small">Detail / Rincian</label>
                                    <input type="text" name="services[0][service_details]" class="form-control form-control-sm" placeholder="Contoh: Jam 07:10 - 10:25, Kelas Ekonomi, Bagasi 20kg">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small required">Biaya Penawaran (Rp)</label>
                                    <input type="number" step="0.01" name="services[0][estimated_cost]" class="form-control form-control-sm service-cost" placeholder="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Ringkasan Penawaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Total Estimasi Biaya</label>
                            <div class="h3 fw-bold text-primary mb-0" id="totalEstimatedCost">Rp 0</div>
                        </div>
                        <div class="alert alert-info small mb-4">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Setelah disimpan, paket penawaran ini dapat dibandingkan dan dipilih di halaman <strong>Evaluasi Penawaran</strong>.
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Paket Penawaran
                            </button>
                            <a href="{{ route('travel.inquiries.show', $inquiry) }}" class="btn btn-outline-secondary">
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
    let serviceCount = 1;
    const addBtn = document.getElementById('addServiceBtn');
    const container = document.getElementById('serviceList');
    const totalDisplay = document.getElementById('totalEstimatedCost');

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.service-cost').forEach(input => {
            const val = parseFloat(input.value) || 0;
            total += val;
        });
        totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    container.addEventListener('input', function (e) {
        if (e.target.classList.contains('service-cost')) {
            calculateTotal();
        }
    });

    addBtn.addEventListener('click', function () {
        const index = serviceCount++;
        const itemHtml = `
            <div class="service-item border rounded p-3 mb-3 bg-light" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-primary service-title">Layanan #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-service-btn">Hapus</button>
                </div>
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small required">Jenis Layanan</label>
                        <select name="services[${index}][service_type]" class="form-select form-select-sm" required>
                            <option value="flight">Tiket Pesawat</option>
                            <option value="hotel">Hotel / Penginapan</option>
                            <option value="car_rental">Rental Mobil / Transport</option>
                            <option value="train">Kereta Api</option>
                            <option value="bus">Bus Travel</option>
                            <option value="ferry">Kapal Laut</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small required">Nama Layanan / Maskapai / Hotel</label>
                        <input type="text" name="services[${index}][service_name]" class="form-control form-control-sm" placeholder="Contoh: Hotel Aston Balikpapan" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small required">Tanggal Pelaksanaan</label>
                        <input type="date" name="services[${index}][service_date]" class="form-control form-control-sm" value="{{ $inquiry->travelRequest->departure_date ? $inquiry->travelRequest->departure_date->format('Y-m-d') : date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small">Detail / Rincian</label>
                        <input type="text" name="services[${index}][service_details]" class="form-control form-control-sm" placeholder="Contoh: 1 Kamar Deluxe, termasuk sarapan">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small required">Biaya Penawaran (Rp)</label>
                        <input type="number" step="0.01" name="services[${index}][estimated_cost]" class="form-control form-control-sm service-cost" placeholder="0" required>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-service-btn')) {
            e.target.closest('.service-item').remove();
            calculateTotal();
            const items = container.querySelectorAll('.service-item');
            items.forEach((item, idx) => {
                item.querySelector('.service-title').innerText = `Layanan #${idx + 1}`;
            });
        }
    });
});
</script>
@endsection
