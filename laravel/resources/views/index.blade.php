@extends('layouts.app')

@section('title', 'TechShop - Najbolja tehnologija u Hrvatskoj')

@php
$ikoneKategorija = [
    'Laptop' => 'bi-laptop',
    'Računalo' => 'bi-pc-display',
    'Komponente' => 'bi-cpu',
    'Pohrana' => 'bi-device-hdd',
    'Oprena za računala' => 'bi-keyboard',
    'Mobiteli' => 'bi-phone',
    'Tableti' => 'bi-tablet',
    'TV' => 'bi-tv',
    'Audi i Video' => 'bi-speaker',
    'Gaming' => 'bi-controller',
    'Mreža' => 'bi-router',
];
@endphp

@section('content')

<!-- Hero Section -->
<section class="hero-section text-white text-center py-5 position-relative overflow-hidden">
    <div class="hero-overlay"></div>
    <div class="container py-5 position-relative">
        <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">
            <i class="bi bi-lightning-fill me-1"></i> Besplatna dostava iznad 50€
        </span>
        <h1 class="display-3 fw-bold mb-3">Dobrodošli u <span class="text-gradient">TechShop</span></h1>
        <p class="lead mb-4 mx-auto" style="max-width: 600px;">
            Najbolja tehnologija po povoljnim cijenama. Istraži, usporedi i pronađi savršen uređaj za sebe.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('proizvodi.index') }}" class="btn btn-primary btn-lg rounded-pill px-4">
                <i class="bi bi-shop me-2"></i> Pregledaj proizvode
            </a>
            <a href="{{ route('pc-builder.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                <i class="bi bi-pc-display me-2"></i> Sastavi PC
            </a>
        </div>
    </div>
</section>

<!-- Zašto mi? -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="feature-box p-3">
                    <div class="feature-icon bg-primary-subtle text-primary rounded-circle mx-auto mb-3">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                    <h6 class="fw-bold">Brza dostava</h6>
                    <small class="text-muted">Dostava u 24-48h</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-box p-3">
                    <div class="feature-icon bg-success-subtle text-success rounded-circle mx-auto mb-3">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <h6 class="fw-bold">Garancija</h6>
                    <small class="text-muted">2 godine na sve proizvode</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-box p-3">
                    <div class="feature-icon bg-warning-subtle text-warning rounded-circle mx-auto mb-3">
                        <i class="bi bi-credit-card fs-4"></i>
                    </div>
                    <h6 class="fw-bold">Sigurno plaćanje</h6>
                    <small class="text-muted">Kartica, PayPal, pouzeće</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-box p-3">
                    <div class="feature-icon bg-info-subtle text-info rounded-circle mx-auto mb-3">
                        <i class="bi bi-headset fs-4"></i>
                    </div>
                    <h6 class="fw-bold">Podrška</h6>
                    <small class="text-muted">Tu smo za vas 24/7</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popularne kategorije -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Popularne kategorije</h2>
            <p class="text-muted">Pronađi što tražiš u našim kategorijama</p>
        </div>
        <div class="row g-4">
            @foreach($kategorije->take(6) as $kat)
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm category-card text-center rounded-4">
                        <div class="card-body py-4">
                            <div class="category-icon mx-auto mb-3">
                                <i class="bi {{ $ikoneKategorija[$kat->ImeKategorija] ?? 'bi-grid' }}"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">{{ $kat->ImeKategorija }}</h6>
                            <a href="{{ url('/kategorija/'.$kat->id_kategorija) }}" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Izdvojeni proizvodi -->
<section id="featured-products" class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Izdvojeni proizvodi</h2>

        <!-- Scrollable row -->
        <div id="product-row" class="d-flex gap-4 overflow-hidden pb-3"
             style="white-space: nowrap; scroll-behavior: smooth;">
            @foreach ($proizvodi as $product)
                <div class="card product-card flex-shrink-0 border-0 shadow-sm"
                     style="width: 250px; border-radius: 1rem; overflow: hidden;">

                    <!-- Image as link -->
                    <a href="{{ route('proizvod.show', $product->Proizvod_ID) }}" class="text-decoration-none">
                        <div class="position-relative" style="height: 200px; overflow: hidden;">
                            <img src="{{ $product->slika_url }}" 
                                 alt="{{ $product->Naziv }}"
                                 class="w-100 h-100 object-fit-cover"
                                 style="transition: transform 0.4s ease;">
                        </div>
                    </a>

                    <!-- Product Info -->
                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-1">{{ $product->Naziv }}</h6>
                        <p class="text-muted small mb-2">{{ Str::limit($product->Opis, 60) }}</p>
                        <h5 class="text-primary fw-bold mb-3">{{ number_format($product->Cijena, 2) }} €</h5>
                    </div>

                    <!-- Add to Cart -->
                    <div class="card-footer text-center bg-white border-0 pb-3">
                        <form action="{{ route('cart.add', ['id' => $product->Proizvod_ID]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-cart-plus me-1"></i> Dodaj u košaricu
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- "View all" button -->
        <div class="text-center mt-5">
            <a href="{{ route('proizvodi.index') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                Pogledaj sve proizvode i kategorije
            </a>
        </div>
    </div>
</section>

<!-- Nedavno pregledano -->
<section id="recently-viewed" class="py-5 bg-light d-none">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2 text-primary"></i>Nedavno pregledano
            </h2>
            <button onclick="clearRecentlyViewed()" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="bi bi-x-lg me-1"></i> Očisti
            </button>
        </div>

        <div id="recently-viewed-products" class="row g-4">
            <!-- Proizvodi će biti učitani putem JS-a -->
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadRecentlyViewed();
});

async function loadRecentlyViewed() {
    const viewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    const section = document.getElementById('recently-viewed');
    const container = document.getElementById('recently-viewed-products');

    if (viewed.length === 0) {
        section.classList.add('d-none');
        return;
    }

    try {
        const response = await fetch('{{ route("proizvodi.byIds") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: viewed })
        });

        const products = await response.json();

        if (products.length === 0) {
            section.classList.add('d-none');
            return;
        }

        container.innerHTML = products.map(product => `
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <a href="${product.url}" class="text-decoration-none">
                        <div class="position-relative overflow-hidden" style="height: 180px;">
                            <img src="${product.slika}"
                                 alt="${product.naziv}"
                                 class="w-100 h-100 object-fit-cover"
                                 style="transition: transform 0.3s ease;">
                        </div>
                    </a>
                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem;">
                            ${product.naziv.length > 40 ? product.naziv.substring(0, 40) + '...' : product.naziv}
                        </h6>
                        <p class="text-primary fw-bold mb-0">${product.cijena} €</p>
                    </div>
                </div>
            </div>
        `).join('');

        section.classList.remove('d-none');

    } catch (error) {
        console.error('Greška pri učitavanju nedavno pregledanih:', error);
        section.classList.add('d-none');
    }
}

function clearRecentlyViewed() {
    localStorage.removeItem('recentlyViewed');
    document.getElementById('recently-viewed').classList.add('d-none');
}
</script>

<style>
/* Hero Section */
.hero-section {
    background: var(--ts-gradient-dark);
    min-height: 400px;
    display: flex;
    align-items: center;
}
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}
.text-gradient {
    background: linear-gradient(90deg, #a5b4fc, #c4b5fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Feature boxes */
.feature-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.feature-box {
    transition: transform 0.2s ease;
}
.feature-box:hover {
    transform: translateY(-3px);
}

/* Category cards */
.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border-radius: var(--ts-radius-lg) !important;
}
.category-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--ts-shadow-xl);
}
.category-icon {
    width: 70px;
    height: 70px;
    background: var(--ts-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}
.category-icon i {
    font-size: 1.8rem;
    color: white;
}
.category-card:hover .category-icon {
    transform: scale(1.1) rotate(5deg);
    background: var(--ts-gradient-hover);
}

/* Product cards */
.product-card {
    transition: all 0.3s ease;
    border-radius: var(--ts-radius-lg) !important;
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--ts-shadow-xl);
}
.product-card:hover img {
    transform: scale(1.07);
}

/* Hide scrollbar */
#product-row::-webkit-scrollbar { display: none; }
#product-row { -ms-overflow-style: none; scrollbar-width: none; }

/* Typography */
h2 { font-weight: 700; }
</style>

@endsection
