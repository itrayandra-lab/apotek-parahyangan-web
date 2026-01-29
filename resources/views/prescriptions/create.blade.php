@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-rose-50 to-gray-100 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Upload Resep Dokter
                </h1>
                <p class="text-lg text-gray-600">
                    Unggah foto resep Anda dan tunggu verifikasi dari apoteker kami
                </p>
            </div>

            <!-- Info Card -->
            <div class="glass-panel p-6 mb-8 border-l-4 border-rose-500">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Penting
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Hanya untuk obat keras yang memerlukan resep dokter</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Pengambilan hanya di <strong>Apotek Parahyangan - Gedung Soho</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><strong>Wajib membawa resep fisik asli</strong> saat pengambilan obat</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Pastikan foto resep jelas dan terbaca (maksimal 5MB)</span>
                    </li>
                </ul>
            </div>

            <!-- Upload Form -->
            <div class="glass-panel p-8">
                <form action="{{ route('prescriptions.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <!-- Image Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-900 mb-3">
                            Foto Resep <span class="text-rose-500">*</span>
                        </label>
                        
                        <div class="relative">
                            <input 
                                type="file" 
                                name="prescription_image" 
                                id="prescription_image" 
                                accept="image/jpeg,image/jpg,image/png"
                                class="hidden"
                                required
                            >
                            
                            <div 
                                id="dropZone"
                                class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer transition-all hover:border-rose-500 hover:bg-rose-50/50"
                            >
                                <div id="uploadPrompt">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-gray-700 font-medium mb-1">Klik untuk upload atau drag & drop</p>
                                    <p class="text-sm text-gray-500">JPG, JPEG, atau PNG (Maks. 5MB)</p>
                                </div>
                                
                                <div id="imagePreview" class="hidden">
                                    <img src="" alt="Preview" class="max-h-64 mx-auto rounded-lg shadow-lg mb-4">
                                    <button 
                                        type="button" 
                                        id="removeImage"
                                        class="text-sm text-rose-600 hover:text-rose-700 font-medium"
                                    >
                                        Ganti Gambar
                                    </button>
                                </div>
                            </div>
                        </div>

                        @error('prescription_image')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- User Notes -->
                    <div class="mb-6">
                        <label for="user_notes" class="block text-sm font-medium text-gray-900 mb-3">
                            Catatan Tambahan (Opsional)
                        </label>
                        <textarea 
                            name="user_notes" 
                            id="user_notes" 
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all"
                            placeholder="Contoh: Alergi terhadap obat tertentu, kondisi khusus, dll."
                        >{{ old('user_notes') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Maksimal 1000 karakter</p>
                        
                        @error('user_notes')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <a 
                            href="{{ route('prescriptions.index') }}" 
                            class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all text-center"
                        >
                            Batal
                        </a>
                        <button 
                            type="submit"
                            class="flex-1 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl"
                        >
                            Upload Resep
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('prescription_image');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const imagePreview = document.getElementById('imagePreview');
    const removeImageBtn = document.getElementById('removeImage');

    // Click to upload
    dropZone.addEventListener('click', (e) => {
        if (e.target !== removeImageBtn && !e.target.closest('#removeImage')) {
            fileInput.click();
        }
    });

    // Drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-rose-500', 'bg-rose-50/50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-rose-500', 'bg-rose-50/50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-rose-500', 'bg-rose-50/50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Remove image
    removeImageBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        uploadPrompt.classList.remove('hidden');
        imagePreview.classList.add('hidden');
    });

    function handleFileSelect(file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            alert('Format file harus JPG, JPEG, atau PNG');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.querySelector('img').src = e.target.result;
            uploadPrompt.classList.add('hidden');
            imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
