@extends('layouts.app')

@section('title', 'Konfiguriraj svoj PC - TechShop')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="text-center mb-4">
        <h1 class="display-5 fw-bold">
            <i class="bi bi-pc-display-horizontal text-primary me-2"></i>
            Konfiguriraj svoj PC
        </h1>
        <p class="text-muted">Odaberi komponente korak po korak i sastavi svoje idealno računalo</p>
    </div>

    <!-- Progress Steps -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap" id="progress-steps">
                @foreach($componentTypes as $index => $type)
                <div class="step-item text-center px-2 {{ $index === 0 ? 'active' : '' }}"
                     data-step="{{ $type->slug }}"
                     data-type-id="{{ $type->id }}"
                     data-required="{{ $type->obavezan ? 'true' : 'false' }}">
                    <div class="step-icon mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle
                                {{ $index === 0 ? 'bg-primary text-white' : 'bg-light text-muted' }}"
                         style="width: 45px; height: 45px; cursor: pointer;">
                        <i class="bi {{ $type->ikona }}" style="font-size: 1.2rem;"></i>
                    </div>
                    <small class="d-none d-md-block {{ $index === 0 ? 'fw-semibold text-primary' : 'text-muted' }}">
                        {{ $type->naziv }}
                    </small>
                    <span class="step-check d-none text-success"><i class="bi bi-check-circle-fill"></i></span>
                </div>
                @if(!$loop->last)
                <div class="step-connector flex-grow-1 d-none d-lg-block" style="height: 2px; background: #dee2e6; margin-top: -20px;"></div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left: Component Selection -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="step-title">
                            <i class="bi bi-cpu text-primary me-2"></i>
                            Odaberi Procesor
                        </h5>
                        <span class="badge bg-primary" id="step-counter">Korak 1 od {{ count($componentTypes) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Min. cijena (€)</label>
                            <input type="number" class="form-control form-control-sm" id="filter-min-price" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Max. cijena (€)</label>
                            <input type="number" class="form-control form-control-sm" id="filter-max-price" placeholder="9999">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-outline-primary btn-sm w-100" id="apply-filters">
                                <i class="bi bi-funnel me-1"></i> Filtriraj
                            </button>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div id="products-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Učitavanje...</span>
                            </div>
                            <p class="text-muted mt-2">Učitavanje komponenti...</p>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button class="btn btn-outline-secondary" id="btn-prev" disabled>
                            <i class="bi bi-arrow-left me-1"></i> Natrag
                        </button>
                        <button class="btn btn-primary" id="btn-next">
                            Dalje <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Configuration Summary (Sticky) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 80px;">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check me-2"></i>
                            Tvoja konfiguracija
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" id="configuration-list">
                            @foreach($componentTypes as $type)
                            <li class="list-group-item d-flex justify-content-between align-items-center config-item"
                                data-type-id="{{ $type->id }}"
                                data-type-slug="{{ $type->slug }}">
                                <div>
                                    <i class="bi {{ $type->ikona }} text-muted me-2"></i>
                                    <span class="component-name text-muted">{{ $type->naziv }}</span>
                                    <small class="d-block text-muted component-product" style="margin-left: 1.5rem;">
                                        Nije odabrano
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="component-price fw-semibold">-</span>
                                    <button class="btn btn-sm btn-link text-danger p-0 ms-2 remove-component d-none"
                                            data-type-id="{{ $type->id }}" title="Ukloni">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-light">
                        <!-- Power Warning -->
                        <div class="alert alert-warning py-2 px-3 mb-2 d-none" id="power-warning">
                            <small>
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <span id="power-warning-text"></span>
                            </small>
                        </div>

                        <!-- Total Price -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">UKUPNO:</span>
                            <span class="h4 mb-0 text-primary fw-bold" id="total-price">0.00 €</span>
                        </div>

                        <!-- Actions -->
                        <button class="btn btn-success w-100 mb-2" id="btn-add-to-cart" disabled>
                            <i class="bi bi-cart-plus me-2"></i>
                            Dodaj sve u košaricu
                        </button>

                        @auth
                        <button class="btn btn-outline-primary w-100" id="btn-save-config">
                            <i class="bi bi-save me-2"></i>
                            Spremi konfiguraciju
                        </button>
                        @else
                        <p class="text-muted small text-center mb-0 mt-2">
                            <a href="{{ route('login') }}">Prijavi se</a> za spremanje konfiguracije
                        </p>
                        @endauth
                    </div>
                </div>

                <!-- TDP Info Card -->
                <div class="card shadow-sm mt-3">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-lightning-charge me-1"></i>
                                Procjena potrošnje:
                            </small>
                            <span class="fw-semibold" id="total-tdp">0W</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-plug me-1"></i>
                                Preporučeno napajanje:
                            </small>
                            <span class="fw-semibold text-primary" id="recommended-wattage">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Configuration Modal -->
<div class="modal fade" id="saveConfigModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Spremi konfiguraciju</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="config-name" class="form-label">Naziv konfiguracije</label>
                    <input type="text" class="form-control" id="config-name"
                           placeholder="npr. Gaming PC 2024">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                <button type="button" class="btn btn-primary" id="confirm-save-config">
                    <i class="bi bi-save me-1"></i> Spremi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Finish Configuration Modal -->
<div class="modal fade" id="finishConfigModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Završi konfiguraciju</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Odabrali ste sve komponente za svoju konfiguraciju.</p>
                <p><strong>Sve odabrane komponente bit će dodane u košaricu.</strong></p>
                <p class="text-muted mb-0">Nakon toga možete nastaviti s kupnjom ili nastaviti pregledavati trgovinu.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                <button type="button" class="btn btn-success" id="confirm-finish-config">
                    <i class="bi bi-cart-plus me-1"></i> Dodaj u košaricu
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.step-item {
    transition: all 0.3s ease;
    position: relative;
}
.step-item:hover .step-icon {
    transform: scale(1.1);
}
.step-item.active .step-icon {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
}
.step-item.completed .step-icon {
    background-color: #198754 !important;
    color: white !important;
}
.step-item.completed small {
    color: #198754 !important;
}
.step-check {
    position: absolute;
    top: -5px;
    right: 5px;
}
.config-item.selected {
    background-color: #e8f4fd;
}
.config-item.selected .component-name {
    color: #0d6efd !important;
    font-weight: 600;
}
.product-card {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 2px solid transparent;
}
.product-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
}
.product-card.selected {
    border-color: #198754;
    background-color: #d1e7dd;
}
.product-card.incompatible {
    opacity: 0.5;
    pointer-events: none;
}
</style>

<script src="{{ asset('js/pc-builder.js') }}"></script>
@endsection
