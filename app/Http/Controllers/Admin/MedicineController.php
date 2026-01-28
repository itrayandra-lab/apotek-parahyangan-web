<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineController extends Controller
{
    /**
     * Display a listing of medicines.
     */
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $query = Medicine::with(['category', 'medicineUnits', 'media']);

        if ($request->ajax()) {
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('details', function ($medicine) {
                    $imgHtml = '<div class="w-16 h-16 rounded-2xl overflow-hidden shadow-sm bg-gray-100 flex-shrink-0 relative">';
                    if ($medicine->hasImage()) {
                        $imgHtml .= '<img src="' . $medicine->getImageUrl() . '" class="w-full h-full object-cover" alt="' . $medicine->name . '">';
                    } else {
                        $imgHtml .= '<div class="w-full h-full flex items-center justify-center text-gray-300 bg-rose-50">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>';
                    }
                    $imgHtml .= '</div>';

                    $classificationHtml = '';
                    if($medicine->classification) {
                        $colorClass = $medicine->classification === 'Obat Keras' ? 'text-rose-500' : 'text-emerald-500';
                        $classificationHtml = '<span class="w-1 h-1 rounded-full bg-gray-300 mx-2"></span>
                                               <span class="text-[9px] font-bold uppercase tracking-widest ' . $colorClass . '">
                                                   ' . $medicine->classification . '
                                               </span>';
                    }

                    return '<div class="flex items-center gap-5">
                                ' . $imgHtml . '
                                <div>
                                    <div class="text-base font-bold text-gray-900 font-display mb-1">' . $medicine->name . '</div>
                                    <div class="flex items-center">
                                        <div class="text-xs text-gray-400 font-mono tracking-wider uppercase">' . $medicine->code . '</div>
                                        ' . $classificationHtml . '
                                    </div>
                                </div>
                            </div>';
                })
                ->addColumn('category_label', function ($medicine) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                ' . ($medicine->category->name ?? 'Uncategorized') . '
                            </span>';
                })
                ->addColumn('stock_info', function ($medicine) {
                    $lowStockHtml = '';
                    if ($medicine->total_stock_unit <= $medicine->min_stock_alert) {
                        $lowStockHtml = '<span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1">Low Stock</span>';
                    }
                    return '<div class="flex flex-col">
                                <span class="font-mono font-bold text-gray-700">' . $medicine->total_stock_unit . ' ' . $medicine->base_unit . '</span>
                                ' . $lowStockHtml . '
                            </div>';
                })
                ->addColumn('price_info', function ($medicine) {
                    return '<span class="font-bold text-gray-900">' . $medicine->formatted_price . '</span>';
                })
                ->addColumn('actions', function ($medicine) {
                    return '<a href="' . route('admin.medicines.edit', $medicine->id) . '" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>';
                })
                ->rawColumns(['details', 'category_label', 'stock_info', 'price_info', 'actions'])
                ->make(true);
        }

        $categories = MedicineCategory::orderBy('name', 'asc')->get();

        return view('admin.medicines.index', compact('categories'));
    }

    /**
     * Show the form for editing the specified medicine.
     */
    public function edit(int $id): View
    {
        $medicine = Medicine::findOrFail($id);
        $categories = MedicineCategory::orderBy('name', 'asc')->get();

        return view('admin.medicines.edit', compact('medicine', 'categories'));
    }

    /**
     * Update the specified medicine in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'classification' => 'nullable|string|in:Bebas,Bebas Terbatas,Obat Keras',
            'indication' => 'nullable|string',
            'composition' => 'nullable|string',
            'dosage' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'bpom_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB
        ]);

        // Update medical info fields
        $medicine->update($request->only([
            'classification',
            'indication',
            'composition',
            'dosage',
            'side_effects',
            'bpom_number',
            'manufacturer',
        ]));

        // Handle image upload
        if ($request->hasFile('image')) {
            $medicine->clearMediaCollection('medicine_images');
            $medicine->addMediaFromRequest('image')
                ->toMediaCollection('medicine_images');
        }

        return redirect()->route('admin.medicines.index')->with('success', 'Data informasi obat ' . $medicine->name . ' berhasil diperbarui.');
    }
}
