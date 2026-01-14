@extends('admin.layouts.app')

@section('title', 'Edit Informasi Obat - ' . $medicine->name)

@section('content')
    <div class="section-container section-padding">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12">
                <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-rose-500 font-bold text-xs uppercase tracking-widest transition-colors mb-6 group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Medicines
                </a>
                <h1 class="text-4xl font-display font-medium uppercase text-gray-900 mb-2">
                    Enrich Medicine Data
                </h1>
                <p class="text-gray-500 font-light text-lg">
                    Lengkapi informasi medis dan legalitas untuk <strong>{{ $medicine->name }}</strong>.
                </p>
            </div>

            <form action="{{ route('admin.medicines.update', $medicine->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    {{-- Left Side: Image & Info --}}
                    <div class="lg:col-span-5 space-y-8">
                        {{-- Image Upload --}}
                        <div class="glass-panel rounded-[2.5rem] p-8">
                            <h3 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-6">Product Visual</h3>
                            <div class="space-y-6">
                                <div class="w-full aspect-square rounded-3xl overflow-hidden bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center relative shadow-inner group">
                                    @if($medicine->hasImage())
                                        <img id="image-preview" src="{{ $medicine->getImageUrl() }}" class="w-full h-full object-cover" alt="Preview">
                                    @else
                                        <div id="placeholder" class="text-center p-8">
                                            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-sm text-gray-400">No image uploaded</p>
                                        </div>
                                        <img id="image-preview" src="#" class="w-full h-full object-cover hidden" alt="Preview">
                                    @endif
                                    
                                    <label for="image-input" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer backdrop-blur-sm">
                                        <span class="bg-white px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest">Change Image</span>
                                    </label>
                                    <input type="file" name="image" id="image-input" accept="image/*" class="hidden">
                                </div>
                                @error('image') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Metadata --}}
                        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white">
                            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-6">Automatic POS Data</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-white/10 pb-3">
                                    <span class="text-xs text-gray-400">SKU Code</span>
                                    <span class="text-xs font-mono">{{ $medicine->code }}</span>
                                </div>
                                <div class="flex justify-between border-b border-white/10 pb-3">
                                    <span class="text-xs text-gray-400">Current Stock</span>
                                    <span class="text-xs font-bold">{{ $medicine->total_stock_unit }} {{ $medicine->base_unit }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-400">Price</span>
                                    <span class="text-xs font-bold">{{ $medicine->formatted_price }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Medical & Regulatory Form --}}
                    <div class="lg:col-span-7 space-y-8">
                        <div class="glass-panel rounded-[2.5rem] p-8 md:p-10">
                            <h3 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-8">Medical Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                {{-- Classification --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Golongan Obat</label>
                                    <select name="classification" class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all">
                                        <option value="">Pilih Golongan</option>
                                        <option value="Bebas" @selected($medicine->classification === 'Bebas')>Bebas (Hijau)</option>
                                        <option value="Bebas Terbatas" @selected($medicine->classification === 'Bebas Terbatas')>Bebas Terbatas (Biru)</option>
                                        <option value="Obat Keras" @selected($medicine->classification === 'Obat Keras')>Obat Keras (Merah / K)</option>
                                    </select>
                                </div>
                                {{-- BPOM --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">No. Registrasi BPOM</label>
                                    <input type="text" name="bpom_number" value="{{ old('bpom_number', $medicine->bpom_number) }}" class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all" placeholder="GKLXXXXXXXXX">
                                </div>
                            </div>

                            <div class="space-y-6">
                                {{-- Manufacturer --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Produsen / Manufaktur</label>
                                    <input type="text" name="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}" class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all">
                                </div>

                                {{-- Indikasi --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Indikasi Umum</label>
                                    <textarea name="indication" rows="3" class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all resize-none">{{ old('indication', $medicine->indication) }}</textarea>
                                </div>

                                {{-- Komposisi --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Komposisi</label>
                                    <textarea name="composition" rows="2" class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all resize-none">{{ old('composition', $medicine->composition) }}</textarea>
                                </div>

                                {{-- Dosis --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Dosis & Aturan Pakai</label>
                                    <textarea name="dosage" rows="3" class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 placeholder:italic placeholder:text-gray-300 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all resize-none" placeholder="Contoh: Dewasa 1 tablet, 3 kali sehari sesudah makan">{{ old('dosage', $medicine->dosage) }}</textarea>
                                </div>

                                {{-- Side Effects --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Kontraindikasi & Efek Samping</label>
                                    <textarea name="side_effects" rows="3" class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all resize-none">{{ old('side_effects', $medicine->side_effects) }}</textarea>
                                </div>
                            </div>

                            <div class="pt-10 flex gap-4">
                                <a href="{{ route('admin.medicines.index') }}" class="flex-1 px-8 py-5 bg-gray-100 text-gray-600 rounded-2xl font-bold uppercase tracking-widest text-xs text-center hover:bg-gray-200 transition-all">
                                    Cancel
                                </a>
                                <button type="submit" class="flex-1 px-8 py-5 bg-gray-900 text-white rounded-2xl font-bold uppercase tracking-widest text-xs shadow-xl shadow-gray-200 hover:bg-rose-500 transition-all">
                                    Update Medicine Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');
            const placeholder = document.getElementById('placeholder');

            if (imageInput) {
                imageInput.onchange = evt => {
                    const [file] = imageInput.files;
                    if (file) {
                        const previewUrl = URL.createObjectURL(file);
                        if (imagePreview) {
                            imagePreview.src = previewUrl;
                            imagePreview.classList.remove('hidden');
                        }
                        if (placeholder) {
                            placeholder.classList.add('hidden');
                        }
                    }
                }
            }
        });
    </script>
    @endpush
@endsection
