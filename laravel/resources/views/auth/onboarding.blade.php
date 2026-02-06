@extends('layouts.app_stepform')

@section('title', 'Dovršite profil — TechShop')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg rounded-4 mx-auto" style="max-width: 700px;">
        <div class="card-body p-5">
            <h3 class="fw-bold text-center text-primary mb-4">
                <i class="bi bi-person-lines-fill me-2"></i> Dovršite svoj profil
            </h3>
            <p class="text-muted text-center mb-4">Molimo vas da unesete osnovne podatke za dostavu i kontakt.</p>

            <form method="POST" action="{{ route('onboarding.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ime</label>
                        <input type="text" name="ime" value="{{ old('ime', Auth::user()->ime) }}"
                               class="form-control rounded-pill" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prezime</label>
                        <input type="text" name="prezime" value="{{ old('prezime', Auth::user()->prezime) }}"
                               class="form-control rounded-pill" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telefon</label>
                        <input type="text" name="telefon" value="{{ old('telefon', Auth::user()->telefon) }}"
                               class="form-control rounded-pill">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Adresa</label>
                        <input type="text" name="adresa" value="{{ old('adresa') }}"
                               class="form-control rounded-pill" required>
                    </div>

                    {{-- Grad --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Grad</label>
                        <input type="text" name="grad" id="grad" class="form-control rounded-pill"
                               placeholder="Upiši grad..." autocomplete="off" required>
                    </div>

                    {{-- Poštanski broj --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Poštanski broj</label>
                        <input type="text" name="postanski_broj" id="postanski_broj"
                               class="form-control rounded-pill" required>
                    </div>

                    {{-- Država dropdown --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Država</label>
                        <input list="country-list" name="drzava" id="drzava"
       class="form-control rounded-pill"
       placeholder="Upiši ili odaberi..." required>

                        <datalist id="country-list">
                            @foreach($countries as $country)
                                <option value="{{ $country->name }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Spremi i nastavi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('onboarding script loaded');

    const cityInput = document.getElementById('grad');
    const postalInput = document.getElementById('postanski_broj');

    // If your onboarding page doesn't have a country input, we fall back to HR.
    const countryInput = document.getElementById('drzava');
    const getCountry = () => (countryInput?.value || 'HR').trim() || 'HR';

    if (!cityInput || !postalInput) {
        console.warn('Missing #grad or #postanski_broj input');
        return;
    }

    let t = null;

    async function lookupPostal(city, country) {
        const res = await fetch(`/post-codes/lookup?city=${encodeURIComponent(city)}&country=${encodeURIComponent(country)}`, {
            headers: { 'Accept': 'application/json' }
        });
        return await res.json();
    }

    cityInput.addEventListener('input', () => {
        clearTimeout(t);
        const city = cityInput.value.trim();
        const country = getCountry();

        if (city.length < 2) return;

        t = setTimeout(async () => {
            try {
                const data = await lookupPostal(city, country);
                if (data.postal_code) {
                    postalInput.value = data.postal_code;
                }
            } catch (e) {}
        }, 350);
    });

    cityInput.addEventListener('blur', async () => {
        const city = cityInput.value.trim();
        const country = getCountry();
        if (!city) return;

        try {
            const data = await lookupPostal(city, country);
            if (data.postal_code) {
                postalInput.value = data.postal_code;
            }
        } catch (e) {}
    });
});
</script>

@endpush
