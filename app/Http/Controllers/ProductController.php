<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\Region;
use App\Models\Status;
use App\Services\ProductService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private ProductService $productService) {}

    public function index(Request $request): View
    {
        $products = $this->productService->getFiltered($request->only([
            'q', 'category', 'region', 'city', 'price_from', 'price_to',
            'lat', 'lng', 'radius',
        ]));

        $mapProducts = Product::with(['region', 'city', 'category.parent'])
            ->whereHas('status', fn ($q) => $q->where('name', '!=', 'Sotildi'))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(['id', 'name', 'price', 'image', 'images', 'latitude', 'longitude', 'region_id', 'city_id', 'category_id'])
            ->limit(500)
            ->get()
            ->map(fn ($p) => [
                'id'       => $p->id,
                'title'    => $p->name,
                'price'    => $p->formatted_price,
                'lat'      => $p->latitude,
                'lng'      => $p->longitude,
                'loc'      => trim(($p->city?->name ?? '') . ', ' . ($p->region?->name ?? ''), ', '),
                'img'      => $p->primary_image_url,
                'url'      => route('products.show', $p),
                'category' => $p->category?->name ?? '',
                'parent'   => $p->category?->parent?->name ?? $p->category?->name ?? '',
            ]);

        return view('products.index', [
            'products'    => $products,
            'mapProducts' => $mapProducts,
            'categories'  => Category::with('children')->whereNull('parent_id')->get(),
            'regions'     => Region::all(),
            'cities'      => City::orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'user', 'color', 'region', 'city', 'status']);

        Product::withoutTimestamps(fn () => $product->increment('views_count'));

        $isFavorited = $product->isFavoritedBy(auth()->id());

        $existingConversation = auth()->check()
            ? Conversation::where('product_id', $product->id)
                ->where('buyer_id', auth()->id())
                ->first()
            : null;

        $contactPhone = $product->contact_phone ?? $product->user?->phone;

        return view('products.show', compact('product', 'isFavorited', 'existingConversation', 'contactPhone'));
    }

    public function create(): View
    {
        return view('products.create', $this->formData());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $images = $request->file('images') ?? [];

        $product = $this->productService->store(
            $request->safe()->except(['images']),
            $images,
            $request->user()->id
        );

        return redirect()->route('products.show', $product)
            ->with('success', "E'lon muvaffaqiyatli joylashtirildi.");
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('products.edit', array_merge(compact('product'), $this->formData()));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $images = $request->file('images') ?? [];

        $this->productService->update(
            $product,
            $request->safe()->except(['images']),
            $images
        );

        return redirect()->route('products.show', $product)
            ->with('success', "E'lon muvaffaqiyatli yangilandi.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->productService->delete($product);

        return redirect()->route('products.index')
            ->with('success', "E'lon o'chirildi.");
    }

    private function formData(): array
    {
        return [
            'categories' => Category::whereNull('parent_id')->with('children')->get(),
            'colors'     => Color::all(),
            'regions'    => Region::all(),
            'cities'     => City::orderBy('name')->get(),
            'statuses'   => Status::where('name', '!=', 'Sotildi')->get(),
        ];
    }
}
