@extends('layouts.app')

@section('title', $proizvod->Naziv . ' — TechShop')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5 align-items-center">
            
            <!-- Product Image -->
            <div class="col-md-6 text-center">
                <div class="border rounded-4 shadow-sm overflow-hidden position-relative" 
                     style="max-width: 450px; margin: 0 auto;">
                    <img src="{{ $proizvod->slika_url }}" 
                         alt="{{ $proizvod->Naziv }}" 
                         class="img-fluid w-100" 
                         style="transition: transform 0.3s ease;">
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <h2 class="fw-bold mb-3">{{ $proizvod->Naziv }}</h2>
                <p class="text-muted fs-6">{{ $proizvod->Opis }}</p>

                <!-- Social proof -->
                <div class="d-inline-flex align-items-center bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-eye-fill me-2"></i>
                    <span class="fw-semibold"><span id="viewers-count">{{ rand(5, 23) }}</span> osoba trenutno gleda ovaj proizvod</span>
                </div>

                <!-- Stock info -->
                @if($proizvod->StanjeNaSkladistu > 0)
                    <p class="text-success fw-semibold mb-1">
                        <i class="bi bi-check-circle me-1"></i> Na zalihi: {{ $proizvod->StanjeNaSkladistu }} kom
                    </p>
                @else
                    <p class="text-danger fw-semibold mb-1">
                        <i class="bi bi-x-circle me-1"></i> Nema na zalihi
                    </p>
                @endif

                <!-- Price -->
                <div class="my-4">
                    <small class="text-secondary d-block">Cijena bez PDV-a: 
                        {{ number_format($proizvod->Cijena / 1.25, 2) }} €
                    </small>
                    <h3 class="fw-bold text-primary mb-0">
                        {{ number_format($proizvod->Cijena, 2) }} €
                    </h3>
                </div>

                <!-- Add to Cart Form -->
                <form action="{{ route('cart.add', ['id' => $proizvod->Proizvod_ID]) }}" 
                      method="POST" 
                      class="d-flex align-items-center gap-3 flex-wrap">
                    @csrf

                    <!-- Quantity Selector -->
                    <div class="input-group quantity-selector" style="width: 160px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="decrementQty()">−</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1"
                            max="{{ $proizvod->StanjeNaSkladistu ?? 10 }}" 
                            class="form-control text-center" 
                            style="width: 60px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="incrementQty()">+</button>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="submit" 
                            class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold shadow-sm"
                            {{ $proizvod->StanjeNaSkladistu <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cart-plus me-2"></i> Dodaj u košaricu
                    </button>
                </form>

                <!-- Back to Products -->
                <div class="mt-4">
                    <a href="{{ route('proizvodi.index') }}" 
                       class="text-decoration-none text-secondary fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Natrag na proizvode
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quantity control script -->
<script>
function incrementQty() {
    const qtyInput = document.getElementById('quantity');
    const max = parseInt(qtyInput.getAttribute('max'));
    let value = parseInt(qtyInput.value);
    if (value < max) qtyInput.value = value + 1;
}

function decrementQty() {
    const qtyInput = document.getElementById('quantity');
    let value = parseInt(qtyInput.value);
    if (value > 1) qtyInput.value = value - 1;
}

// Recently Viewed - spremi ovaj proizvod
(function() {
    const productId = {{ $proizvod->Proizvod_ID }};
    const maxItems = 8;

    let viewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');

    // Ukloni ako već postoji (da ga stavimo na početak)
    viewed = viewed.filter(id => id !== productId);

    // Dodaj na početak
    viewed.unshift(productId);

    // Ograniči na maxItems
    viewed = viewed.slice(0, maxItems);

    localStorage.setItem('recentlyViewed', JSON.stringify(viewed));
})();

// Social proof - povremeno ažuriraj broj gledatelja
(function() {
    const viewersEl = document.getElementById('viewers-count');
    if (!viewersEl) return;

    let current = parseInt(viewersEl.textContent);

    setInterval(() => {
        // Nasumično promijeni za -1, 0, ili +1
        const change = Math.floor(Math.random() * 3) - 1;
        current = Math.max(3, Math.min(25, current + change));
        viewersEl.textContent = current;
    }, 5000); // Svakih 5 sekundi
})();
</script>

<!-- Custom Styles -->
<style>
.quantity-selector button {
    width: 40px;
    transition: all 0.2s ease;
}
.quantity-selector button:hover {
    background: var(--ts-gradient);
    border-color: var(--ts-primary);
    color: white;
}
img:hover {
    transform: scale(1.05);
}
</style>
@endsection
