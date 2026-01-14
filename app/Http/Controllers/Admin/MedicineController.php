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
    public function index(Request $request): View
    {
        $query = Medicine::with(['category', 'medicineUnits', 'media']);

        $search = $request->string('search')->trim();
        $categoryId = $request->integer('category_id');

        if ($search->isNotEmpty()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            });
        }

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        $medicines = $query->orderBy('name', 'asc')
            ->paginate(15)
            ->appends($request->query());

        $categories = MedicineCategory::orderBy('name', 'asc')->get();

        return view('admin.medicines.index', compact('medicines', 'categories', 'search', 'categoryId'));
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
