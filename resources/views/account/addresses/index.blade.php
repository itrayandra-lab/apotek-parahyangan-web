@extends('layouts.app')

@section('title', 'Alamat Saya - Beautylatory')

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="container mx-auto px-6 md:px-8 max-w-4xl">
        <div class="mb-8">
            <a href="{{ route('customer.dashboard') }}" class="text-xs font-bold tracking-widest text-primary uppercase hover:text-primary-dark mb-2 inline-block">&larr; Kembali ke Dashboard</a>
            <p class="text-xs font-bold tracking-[0.2em] text-primary uppercase">Alamat</p>
            <h1 class="text-4xl font-display font-medium text-gray-900 mt-2">Alamat Tersimpan</h1>
            <p class="text-gray-500 mt-2">Kelola alamat pengiriman untuk checkout lebih cepat.</p>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div id="addresses-container" class="space-y-4 mb-6">
            @forelse($addresses as $address)
                <div class="address-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 {{ $address->is_default ? 'ring-2 ring-primary' : '' }}" data-id="{{ $address->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if($address->label)
                                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">{{ $address->label }}</span>
                                @endif
                                @if($address->is_default)
                                    <span class="bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-2 py-1 rounded">Utama</span>
                                @endif
                            </div>
                            <p class="font-semibold text-gray-900">{{ $address->recipient_name }}</p>
                            <p class="text-gray-600">{{ $address->phone }}</p>
                            <p class="text-gray-600 mt-2">{{ $address->full_address }}</p>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            @unless($address->is_default)
                                <button type="button" onclick="setDefault({{ $address->id }})" class="text-xs font-bold tracking-widest text-gray-500 uppercase hover:text-primary">Jadikan Utama</button>
                            @endunless
                            <button type="button" onclick="editAddress({{ $address->id }})" class="text-xs font-bold tracking-widest text-primary uppercase hover:text-primary-dark">Edit</button>
                            <button type="button" onclick="deleteAddress({{ $address->id }})" class="text-xs font-bold tracking-widest text-rose-600 uppercase hover:text-rose-700">Hapus</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                    <p class="text-gray-500">Belum ada alamat tersimpan.</p>
                </div>
            @endforelse
        </div>

        @if($addresses->count() < $maxAddresses)
            <button type="button" onclick="openAddModal()" class="w-full bg-gray-900 text-white py-4 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300">
                + Tambah Alamat Baru
            </button>
        @else
            <p class="text-center text-gray-500 text-sm">Maksimal {{ $maxAddresses }} alamat per akun.</p>
        @endif
    </div>
</div>

<!-- Address Modal -->
<div id="address-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
            <h2 id="modal-title" class="text-2xl font-display font-medium text-gray-900">Tambah Alamat</h2>
        </div>
        <form id="address-form" class="p-6 space-y-4">
            <input type="hidden" id="address-id" value="">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Label (Opsional)</label>
                    <input type="text" id="label" placeholder="Rumah, Kantor, dll" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Nama Penerima</label>
                    <input type="text" id="recipient_name" required class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Nomor Telepon</label>
                <input type="text" id="phone" required class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Alamat Lengkap</label>
                <textarea id="address" rows="2" required class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Provinsi</label>
                    <select id="province_code" required class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                        <option value="">Pilih provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->code }}" data-name="{{ $province->name }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Kota/Kabupaten</label>
                    <select id="city_code" required disabled class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                        <option value="">Pilih kota/kabupaten</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Kecamatan</label>
                    <select id="district_code" required disabled class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                        <option value="">Pilih kecamatan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Kelurahan/Desa</label>
                    <select id="village_code" required disabled class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                        <option value="">Pilih kelurahan/desa</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Kode Pos</label>
                    <input type="text" id="postal_code" required class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div class="flex items-center pt-8">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="is_default" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm font-semibold text-gray-800">Jadikan alamat utama</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-gray-200 transition-all">Batal</button>
                <button type="submit" id="submit-btn" class="flex-1 bg-gray-900 text-white py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('address-modal');
    const form = document.getElementById('address-form');
    const modalTitle = document.getElementById('modal-title');
    const addressIdInput = document.getElementById('address-id');

    const provinceSelect = document.getElementById('province_code');
    const citySelect = document.getElementById('city_code');
    const districtSelect = document.getElementById('district_code');
    const villageSelect = document.getElementById('village_code');

    const routes = {
        cities: '{{ route('indonesia.cities') }}',
        districts: '{{ route('indonesia.districts') }}',
        villages: '{{ route('indonesia.villages') }}',
        store: '{{ route('addresses.store') }}',
        update: '{{ route('addresses.update', ':id') }}',
        destroy: '{{ route('addresses.destroy', ':id') }}',
        setDefault: '{{ route('addresses.set-default', ':id') }}',
    };

    // Region loading functions
    const resetSelect = (select, placeholder) => {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
    };

    const populateSelect = (select, items, placeholder) => {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.code;
            option.dataset.name = item.name;
            option.textContent = item.name;
            select.appendChild(option);
        });
        select.disabled = false;
    };

    const loadCities = async (provinceCode) => {
        resetSelect(citySelect, 'Memuat...');
        resetSelect(districtSelect, 'Pilih kecamatan');
        resetSelect(villageSelect, 'Pilih kelurahan/desa');
        if (!provinceCode) return;

        const response = await axios.get(routes.cities, { params: { province_code: provinceCode } });
        populateSelect(citySelect, response.data?.data ?? [], 'Pilih kota/kabupaten');
    };

    const loadDistricts = async (cityCode) => {
        resetSelect(districtSelect, 'Memuat...');
        resetSelect(villageSelect, 'Pilih kelurahan/desa');
        if (!cityCode) return;

        const response = await axios.get(routes.districts, { params: { city_code: cityCode } });
        populateSelect(districtSelect, response.data?.data ?? [], 'Pilih kecamatan');
    };

    const loadVillages = async (districtCode) => {
        resetSelect(villageSelect, 'Memuat...');
        if (!districtCode) return;

        const response = await axios.get(routes.villages, { params: { district_code: districtCode } });
        populateSelect(villageSelect, response.data?.data ?? [], 'Pilih kelurahan/desa');
    };

    provinceSelect.addEventListener('change', () => loadCities(provinceSelect.value));
    citySelect.addEventListener('change', () => loadDistricts(citySelect.value));
    districtSelect.addEventListener('change', () => loadVillages(districtSelect.value));

    // Modal functions
    window.openAddModal = () => {
        modalTitle.textContent = 'Tambah Alamat';
        addressIdInput.value = '';
        form.reset();
        resetSelect(citySelect, 'Pilih kota/kabupaten');
        resetSelect(districtSelect, 'Pilih kecamatan');
        resetSelect(villageSelect, 'Pilih kelurahan/desa');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.editAddress = async (id) => {
        modalTitle.textContent = 'Edit Alamat';
        addressIdInput.value = id;

        // Fetch address data
        const response = await axios.get('{{ route('addresses.list') }}');
        const address = response.data.data.find(a => a.id === id);
        if (!address) return;

        document.getElementById('label').value = address.label || '';
        document.getElementById('recipient_name').value = address.recipient_name;
        document.getElementById('phone').value = address.phone;
        document.getElementById('address').value = address.address;
        document.getElementById('postal_code').value = address.postal_code;
        document.getElementById('is_default').checked = address.is_default;

        // Load regions
        provinceSelect.value = address.province_code;
        await loadCities(address.province_code);
        citySelect.value = address.city_code;
        await loadDistricts(address.city_code);
        districtSelect.value = address.district_code;
        await loadVillages(address.district_code);
        villageSelect.value = address.village_code;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.deleteAddress = async (id) => {
        if (!confirm('Hapus alamat ini?')) return;

        try {
            await axios.delete(routes.destroy.replace(':id', id));
            location.reload();
        } catch (error) {
            alert(error.response?.data?.message || 'Gagal menghapus alamat.');
        }
    };

    window.setDefault = async (id) => {
        try {
            await axios.post(routes.setDefault.replace(':id', id));
            location.reload();
        } catch (error) {
            alert(error.response?.data?.message || 'Gagal mengubah alamat utama.');
        }
    };

    // Form submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const getSelectedName = (select) => select.options[select.selectedIndex]?.dataset.name || '';

        const data = {
            label: document.getElementById('label').value || null,
            recipient_name: document.getElementById('recipient_name').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            province_code: provinceSelect.value,
            province_name: getSelectedName(provinceSelect),
            city_code: citySelect.value,
            city_name: getSelectedName(citySelect),
            district_code: districtSelect.value,
            district_name: getSelectedName(districtSelect),
            village_code: villageSelect.value,
            village_name: getSelectedName(villageSelect),
            postal_code: document.getElementById('postal_code').value,
            is_default: document.getElementById('is_default').checked,
        };

        const addressId = addressIdInput.value;
        const url = addressId ? routes.update.replace(':id', addressId) : routes.store;
        const method = addressId ? 'put' : 'post';

        try {
            await axios[method](url, data);
            location.reload();
        } catch (error) {
            const message = error.response?.data?.message || 'Gagal menyimpan alamat.';
            alert(message);
        }
    });

    // Close modal on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
});
</script>
@endsection
