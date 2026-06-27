<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Product;
use App\Models\Region;
use App\Models\Status;
use App\Models\Type;
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
            'category', 'region', 'city', 'type', 'price_from', 'price_to',
        ]));

        return view('products.index', [
            'products'   => $products,
            'categories' => Category::with('children')->whereNull('parent_id')->get(),
            'regions'    => Region::all(),
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'user', 'type', 'color', 'region', 'city', 'status']);

        return view('products.show', compact('product'));
    }

    public function create(): View
    {
        return view('products.create', $this->formData());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->store(
            $request->safe()->except('image'),
            $request->file('image'),
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

        $this->productService->update(
            $product,
            $request->safe()->except('image'),
            $request->file('image')
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
            'types'      => Type::all(),
            'colors'     => Color::all(),
            'regions'    => Region::all(),
            'cities'     => City::all(),
            'statuses'   => Status::all(),
        ];
    }
}
