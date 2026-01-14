<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'media']);

        $search = $request->string('search')->trim();
        $categoryId = $request->integer('category_id');
        $imageFilter = $request->string('has_image')->trim();
        $sort = $request->string('sort')->trim();
        $status = $request->string('status')->trim();
        $lowStock = $request->boolean('low_stock');
        $isFeatured = $request->boolean('is_featured');
        $lowStockThreshold = 5;

        if ($search->isNotEmpty()) {
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        if ($imageFilter->isNotEmpty() && in_array($imageFilter->toString(), ['with', 'without'], true)) {
            if ($imageFilter->toString() === 'with') {
                $query->whereHas('media', function ($mediaQuery) {
                    $mediaQuery->where('collection_name', 'product_images');
                });
            } else {
                $query->whereDoesntHave('media', function ($mediaQuery) {
                    $mediaQuery->where('collection_name', 'product_images');
                });
            }
        }

        if ($status->isNotEmpty() && in_array($status->toString(), ['draft', 'published', 'archived'], true)) {
            $query->where('status', $status->toString());
        }

        if ($lowStock) {
            $query->where('stock', '<', $lowStockThreshold);
        }

        if ($isFeatured) {
            $query->where('is_featured', true);
        }

        if ($sort->isNotEmpty()) {
            if ($sort->toString() === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($sort->toString() === 'price_desc') {
                $query->orderBy('price', 'desc');
            } else {
                $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $products = $query
            ->paginate(5)
            ->appends($request->query());

        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'search' => $search->toString(),
                'category_id' => $categoryId > 0 ? $categoryId : null,
                'has_image' => $imageFilter->toString(),
                'sort' => $sort->toString(),
                'status' => $status->toString(),
                'low_stock' => $lowStock,
                'is_featured' => $isFeatured,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::all();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.products.form', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'discount_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB
            'lynk_id_link' => 'nullable|url',
        ]);

        $productData = $request->only([
            'name',
            'category_id',
            'price',
            'discount_price',
            'stock',
            'weight',
            'status',
            'description',
            'lynk_id_link',
        ]);
        $productData['is_featured'] = $request->boolean('is_featured');

        $product = Product::create($productData);

        // Handle image upload via Media Library
        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')
                ->toMediaCollection('product_images');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug): View
    {
        $medicine = Medicine::with(['category', 'mainSupplier', 'medicineUnits', 'stockBatches', 'media'])
            ->where('code', $slug)
            ->first();

        if ($medicine) {
            $relatedMedicines = Medicine::where('category_id', $medicine->category_id)
                ->where('id', '!=', $medicine->id)
                ->take(4)
                ->get();

            return view('medicines.show', [
                'medicine' => $medicine,
                'relatedMedicines' => $relatedMedicines,
            ]);
        }

        // Fallback to old beauty products if needed
        $product = Product::where('slug', $slug)->firstOrFail();
        
        return view('products.show', [
            'product' => $product->load('category'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.products.form', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'discount_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB
            'lynk_id_link' => 'nullable|url',
        ]);

        $productData = $request->only([
            'name',
            'category_id',
            'price',
            'discount_price',
            'stock',
            'weight',
            'status',
            'description',
            'lynk_id_link',
        ]);
        $productData['is_featured'] = $request->boolean('is_featured');

        $product->update($productData);

        // Handle image upload via Media Library (auto-replaces via singleFile())
        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')
                ->toMediaCollection('product_images');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product moved to trash.');
    }

    /**
     * Display a listing of trashed products.
     */
    public function trash(Request $request): View
    {
        $query = Product::onlyTrashed()->with(['category', 'media']);

        $search = $request->string('search')->trim();

        if ($search->isNotEmpty()) {
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        $products = $query
            ->orderBy('deleted_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.products.trash', [
            'products' => $products,
            'filters' => [
                'search' => $search->toString(),
            ],
        ]);
    }

    /**
     * Restore a trashed product.
     */
    public function restore(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.trash')->with('success', 'Product restored successfully.');
    }

    /**
     * Permanently delete a product.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        // Media files automatically deleted via model relationship
        $product->forceDelete();

        return redirect()->route('admin.products.trash')->with('success', 'Product permanently deleted.');
    }

    public function guestIndex(Request $request)
    {
        $query = Medicine::with(['category', 'medicineUnits', 'media'])
            ->whereHas('medicineUnits') // Ensure it has a price
            ->orderBy('name', 'asc');

        // Search filter
        $search = $request->string('search')->trim();
        if ($search->isNotEmpty()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            });
        }

        // Filter by category if specified
        if ($request->has('category') && ! empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12)->appends($request->query());
        
        // Use MedicineCategory instead of Category
        $categories = MedicineCategory::whereHas('medicines')->orderBy('name', 'asc')->get();

        return view('products.index', compact('products', 'categories', 'search'));
    }

    /**
     * Load more products via AJAX
     */
    public function loadMore(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $category = $request->get('category');
        $search = $request->string('search')->trim();

        $query = Medicine::with(['category', 'medicineUnits', 'media'])
            ->orderBy('name', 'asc');

        // Filter by search if specified
        if ($search->isNotEmpty()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            });
        }

        // Filter by category if specified
        if (! empty($category)) {
            $query->where('category_id', $category);
        }

        $products = $query->paginate(12, ['*'], 'page', $page);

        // Transform medicines to match expected JSON structure
        $productsData = array_map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'slug' => $medicine->code,
                'price' => $medicine->price,
                'discount_price' => null,
                'category' => $medicine->category ? [
                    'id' => $medicine->category->id,
                    'name' => $medicine->category->name,
                ] : null,
                'image_url' => $medicine->getImageUrl(),
                'has_image' => $medicine->hasImage(),
                'url' => URL::route('products.show', $medicine->code),
            ];
        }, $products->items());

        return response()->json([
            'products' => $productsData,
            'hasMorePages' => $products->hasMorePages(),
            'nextPageUrl' => $products->nextPageUrl(),
        ]);
    }

    /**
     * Get all products as JSON for API calls.
     */
    public function getAll(): JsonResponse
    {
        $products = Product::with('category')->orderBy('created_at', 'desc')->get();

        return response()->json($products);
    }
}
