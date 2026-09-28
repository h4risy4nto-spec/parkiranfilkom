@extends('layouts.app')

@section('title', 'Pengaturan Tarif Parkir - Admin FILKOM')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Konfigurasi Tarif Parkir</span>
            <span class="badge badge-orange small fs-6">Tarif & Denda</span>
        </h2>
        <p class="text-muted mb-0">Atur skema penagihan tarif parkir per jam serta denda tiket hilang untuk setiap kategori kendaraan.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-dark p-4 shadow">
            <h4 class="fw-bold text-white mb-4 d-flex align-items-center gap-2 border-bottom border-secondary pb-3">
                <i class="bi bi-cash-stack text-warning"></i> Skema Tarif Parkir FILKOM UB
            </h4>

            <form action="{{ route('admin.rates.update') }}" method="POST">
                @csrf
                
                @foreach($rates as $index => $rate)
                <div class="p-3 rounded mb-4 border border-secondary" style="background: rgba(0, 0, 0, 0.25);">
                    <input type="hidden" name="rates[{{ $index }}][id]" value="{{ $rate->id }}">
                    
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-secondary">
                        <i class="bi {{ $rate->vehicle_type === 'Motor' ? 'bi-bicycle text-warning' : 'bi-car-front-fill text-info' }} fs-4"></i>
                        <h5 class="fw-bold text-white mb-0">Kendaraan {{ $rate->vehicle_type }}</h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-light small fw-semibold">Tarif Jam Pertama (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-black border-secondary text-muted">Rp</span>
                                <input type="number" 
                                       name="rates[{{ $index }}][first_hour_rate]" 
                                       class="form-control form-control-dark font-monospace" 
                                       value="{{ (int) $rate->first_hour_rate }}" 
                                       min="0" 
                                       step="500" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-light small fw-semibold">Tarif Per Jam Berikutnya (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-black border-secondary text-muted">Rp</span>
                                <input type="number" 
                                       name="rates[{{ $index }}][hourly_rate]" 
                                       class="form-control form-control-dark font-monospace" 
                                       value="{{ (int) $rate->hourly_rate }}" 
                                       min="0" 
                                       step="500" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-light small fw-semibold">Denda Tiket Hilang (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-black border-secondary text-muted">Rp</span>
                                <input type="number" 
                                       name="rates[{{ $index }}][lost_ticket_penalty]" 
                                       class="form-control form-control-dark font-monospace" 
                                       value="{{ (int) $rate->lost_ticket_penalty }}" 
                                       min="0" 
                                       step="1000" 
                                       required>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="text-end pt-2">
                    <button type="submit" class="btn btn-neon-green btn-lg px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Seluruh Perubahan Tarif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
