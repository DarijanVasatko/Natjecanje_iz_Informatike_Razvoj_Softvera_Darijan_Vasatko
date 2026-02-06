<?php

namespace App\Http\Controllers;

use App\Models\PcComponentType;
use App\Models\PcComponentSpec;
use App\Models\PcConfiguration;
use App\Models\PcConfigurationItem;
use App\Models\Proizvod;
use App\Models\Kosarica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PcBuilderController extends Controller
{
    public function index()
    {
        $componentTypes = PcComponentType::orderBy('redoslijed')->get();
        $configuration = $this->getOrCreateConfiguration();

        return view('pc-builder.index', compact('componentTypes', 'configuration'));
    }

    public function newConfiguration()
    {
        session()->forget('pc_configuration_id');

        $config = PcConfiguration::create([
            'user_id' => Auth::id(),
            'session_id' => Auth::check() ? null : session()->getId(),
        ]);

        session(['pc_configuration_id' => $config->id]);

        return redirect()->route('pc-builder.index')
            ->with('success', 'Nova konfiguracija je spremna!');
    }

    public function getStep(Request $request, $step)
    {
        $componentType = PcComponentType::where('slug', $step)->firstOrFail();
        $configuration = $this->getOrCreateConfiguration();

        $products = Proizvod::whereHas('pcSpec', function ($query) use ($componentType) {
            $query->where('component_type_id', $componentType->id);
        })->with('pcSpec')->get();

        $compatibleProducts = $this->filterCompatibleProducts($products, $configuration, $componentType);
        $currentSelection = $configuration->getComponentByType($componentType->id);

        return response()->json([
            'componentType' => $componentType,
            'products' => $compatibleProducts,
            'currentSelection' => $currentSelection ? $currentSelection->load('proizvod') : null,
        ]);
    }

    public function getCompatibleProducts(Request $request, $typeId)
    {
        $componentType = PcComponentType::findOrFail($typeId);
        $configuration = $this->getOrCreateConfiguration();

        $products = Proizvod::whereHas('pcSpec', function ($query) use ($typeId) {
            $query->where('component_type_id', $typeId);
        })->with('pcSpec')->get();

        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        if ($minPrice !== null) {
            $products = $products->where('Cijena', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $products = $products->where('Cijena', '<=', $maxPrice);
        }

        $compatibleProducts = $this->filterCompatibleProducts($products, $configuration, $componentType);

        return response()->json([
            'products' => $compatibleProducts->values(),
            'componentType' => $componentType,
        ]);
    }

    public function addComponent(Request $request)
    {
        $request->validate([
            'component_type_id' => 'required|exists:pc_component_types,id',
            'proizvod_id' => 'required|exists:proizvod,Proizvod_ID',
        ]);

        $configuration = $this->getOrCreateConfiguration();
        $product = Proizvod::findOrFail($request->proizvod_id);

        $item = $configuration->addComponent(
            $request->component_type_id,
            $request->proizvod_id,
            $product->Cijena
        );

        return response()->json([
            'success' => true,
            'item' => $item->load(['proizvod', 'componentType']),
            'configuration' => $this->getConfigurationData($configuration),
        ]);
    }

    public function removeComponent($typeId)
    {
        $configuration = $this->getOrCreateConfiguration();
        $configuration->removeComponent($typeId);

        return response()->json([
            'success' => true,
            'configuration' => $this->getConfigurationData($configuration),
        ]);
    }

    public function getConfiguration()
    {
        $configuration = $this->getOrCreateConfiguration();
        return response()->json($this->getConfigurationData($configuration));
    }

    public function addAllToCart(Request $request)
    {
        $configuration = $this->getOrCreateConfiguration();
        $items = $configuration->items()->with('proizvod')->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Konfiguracija je prazna.',
            ], 400);
        }

        $addedCount = 0;

        foreach ($items as $item) {
            if (Auth::check()) {
                $cartItem = DB::table('kosarica')
                    ->where('korisnik_id', Auth::id())
                    ->where('proizvod_id', $item->proizvod_id)
                    ->first();

                if ($cartItem) {
                    DB::table('kosarica')
                        ->where('id', $cartItem->id)
                        ->update(['kolicina' => $cartItem->kolicina + 1]);
                } else {
                    DB::table('kosarica')->insert([
                        'korisnik_id' => Auth::id(),
                        'proizvod_id' => $item->proizvod_id,
                        'kolicina' => 1,
                    ]);
                }
            } else {
                $cart = session('cart', []);
                $id = $item->proizvod_id;

                if (isset($cart[$id])) {
                    $cart[$id]['quantity'] += 1;
                } else {
                    $cart[$id] = [
                        'product' => $item->proizvod,
                        'quantity' => 1,
                    ];
                }
                session(['cart' => $cart]);
            }
            $addedCount++;
        }

        if (Auth::check()) {
            $cartCount = DB::table('kosarica')->where('korisnik_id', Auth::id())->sum('kolicina');
        } else {
            $cartCount = collect(session('cart', []))->sum('quantity');
        }

        return response()->json([
            'success' => true,
            'message' => "Dodano {$addedCount} proizvoda u košaricu!",
            'cartCount' => $cartCount,
        ]);
    }

    public function saveConfiguration(Request $request)
    {
        $request->validate([
            'naziv' => 'nullable|string|max:255',
        ]);

        $configuration = $this->getOrCreateConfiguration();
        $configuration->naziv = $request->input('naziv', 'Moja konfiguracija ' . now()->format('d.m.Y H:i'));
        $configuration->user_id = Auth::id();
        $configuration->save();

        return response()->json([
            'success' => true,
            'message' => 'Konfiguracija je spremljena!',
            'configuration' => $configuration,
        ]);
    }

    public function savedConfigurations()
    {
        $configurations = PcConfiguration::where('user_id', Auth::id())
            ->whereNotNull('naziv')
            ->with('items.proizvod', 'items.componentType')
            ->orderByDesc('updated_at')
            ->get();

        return view('pc-builder.saved', compact('configurations'));
    }

    public function loadConfiguration($id)
    {
        $configuration = PcConfiguration::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        session(['pc_configuration_id' => $configuration->id]);

        return redirect()->route('pc-builder.index')
            ->with('success', 'Konfiguracija je učitana!');
    }

    protected function getOrCreateConfiguration(): PcConfiguration
    {
        $configId = session('pc_configuration_id');

        if ($configId) {
            $config = PcConfiguration::find($configId);
            if ($config) {
                if (Auth::check() && !$config->user_id) {
                    $config->user_id = Auth::id();
                    $config->save();
                }
                return $config;
            }
        }

        if (Auth::check()) {
            $config = PcConfiguration::where('user_id', Auth::id())
                ->whereNull('naziv')
                ->first();
        } else {
            $config = PcConfiguration::where('session_id', session()->getId())
                ->whereNull('naziv')
                ->first();
        }

        if ($config) {
            session(['pc_configuration_id' => $config->id]);
            return $config;
        }

        $config = PcConfiguration::create([
            'user_id' => Auth::id(),
            'session_id' => Auth::check() ? null : session()->getId(),
        ]);

        session(['pc_configuration_id' => $config->id]);

        return $config;
    }

    protected function filterCompatibleProducts($products, PcConfiguration $configuration, PcComponentType $componentType)
    {
        $selectedItems = $configuration->items()->with('proizvod.pcSpec')->get();

        if ($selectedItems->isEmpty()) {
            return $products;
        }

        return $products->filter(function ($product) use ($selectedItems) {
            if (!$product->pcSpec) {
                return false;
            }

            foreach ($selectedItems as $item) {
                if ($item->proizvod && $item->proizvod->pcSpec) {
                    if (!$product->pcSpec->isCompatibleWith($item->proizvod->pcSpec)) {
                        return false;
                    }
                }
            }

            return true;
        });
    }

    protected function getConfigurationData(PcConfiguration $configuration): array
    {
        $configuration->load('items.proizvod', 'items.componentType');
        $componentTypes = PcComponentType::orderBy('redoslijed')->get();

        $items = [];
        foreach ($componentTypes as $type) {
            $item = $configuration->items->where('component_type_id', $type->id)->first();
            $items[$type->slug] = $item ? [
                'id' => $item->id,
                'component_type' => $type,
                'proizvod' => $item->proizvod,
                'cijena' => $item->cijena_u_trenutku,
            ] : null;
        }

        return [
            'id' => $configuration->id,
            'items' => $items,
            'ukupna_cijena' => $configuration->ukupna_cijena,
            'is_complete' => $configuration->isComplete(),
            'recommended_wattage' => $configuration->getRecommendedWattage(),
            'total_tdp' => $configuration->getTotalTdp(),
        ];
    }
}
