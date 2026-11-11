@extends('layouts.app')

@section('title', 'Central Travel Queue')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Antrean Permintaan Perjalanan (Travel Queue)</h1>
            <p class="text-muted small mb-0">Antrean terpusat untuk permintaan perjalanan dari SPD Approved dan Pengajuan Proyek</p>
        </div>
        <div>
            @can('travel.site-request')
            <a href="{{ route('travel.requests.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Buat Permintaan Proyek
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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('travel.requests.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_inquiry" {{ request('status') === 'in_inquiry' ? 'selected' : '' }}>In Inquiry</option>
                        <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved / Selected</option>
                        <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Booked</option>
                        <option value="ticket_issued" {{ request('status') === 'ticket_issued' ? 'selected' : '' }}>Ticket Issued</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Sumber</label>
                    <select name="source_type" class="form-select">
                        <option value="">Semua Sumber</option>
                        <option value="spd" {{ request('source_type') === 'spd' ? 'selected' : '' }}>SPD Approved</option>
                        <option value="project" {{ request('source_type') === 'project' ? 'selected' : '' }}>Project Request</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Proyek</label>
                    <select name="project_id" class="form-select">
                        <option value="">Semua Proyek</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->cost_center ?? '' }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="No. Permintaan / Rute / Nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Permintaan</th>
                            <th>Sumber</th>
                            <th>Traveler / Penumpang</th>
                            <th>Rute</th>
                            <th>Jadwal Berangkat</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($travelRequests as $tr)
                            <tr>
                                <td>
                                    <a href="{{ route('travel.requests.show', $tr) }}" class="fw-bold text-decoration-none">
                                        {{ $tr->request_number }}
                                    </a>
                                    <div class="small text-muted">{{ $tr->project ? ($tr->project->cost_center ?? $tr->project->name) : 'General' }}</div>
                                </td>
                                <td>
                                    @if($tr->source_type === 'spd')
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-file-earmark-text"></i> SPD
                                        </span>
                                        @if($tr->spd)
                                            <div class="small text-muted">{{ $tr->spd->spd_number }}</div>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-briefcase"></i> Proyek
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($tr->passengers->count() > 0)
                                        <div><strong>{{ $tr->passengers->first()->passenger_name }}</strong></div>
                                        @if($tr->passengers->count() > 1)
                                            <span class="badge bg-light text-secondary border">+{{ $tr->passengers->count() - 1 }} penumpang lain</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $tr->origin }} &rarr; {{ $tr->destination }}</div>
                                    <div class="small text-muted">{{ ucfirst($tr->travel_category) }}</div>
                                </td>
                                <td>
                                    <div>{{ $tr->departure_date ? $tr->departure_date->format('d/m/Y') : '-' }}</div>
                                    @if($tr->return_date)
                                        <div class="small text-muted">Pulang: {{ $tr->return_date->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($tr->need_flight) <span class="badge bg-primary" title="Pesawat"><i class="bi bi-airplane"></i> Pesawat</span> @endif
                                        @if($tr->need_hotel) <span class="badge bg-success" title="Hotel"><i class="bi bi-building"></i> Hotel</span> @endif
                                        @if($tr->need_local_transport) <span class="badge bg-warning text-dark" title="Transport"><i class="bi bi-car-front"></i> Transport</span> @endif
                                        @if(!$tr->need_flight && !$tr->need_hotel && !$tr->need_local_transport)
                                            <span class="text-muted small">Transport Default</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
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
                                        ][$tr->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $tr->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('travel.requests.show', $tr) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Belum ada permintaan perjalanan dalam antrean.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($travelRequests->hasPages())
            <div class="card-footer bg-white">
                {{ $travelRequests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
