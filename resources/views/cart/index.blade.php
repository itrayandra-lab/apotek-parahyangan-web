@extends('layouts.app')

@section('title', 'Keranjang Belanja - Beautylatory')

@section('content')
    <div class="pt-32 pb-20 min-h-screen bg-gray-50">
        <div class="container mx-auto px-6 md:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <p class="text-xs font-bold tracking-widest text-primary uppercase mb-2">Shopping Bag</p>
                    <h1 class="text-4xl font-display font-medium text-gray-900">Keranjang Kamu</h1>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-bold tracking-widest text-gray-500 hover:text-primary uppercase">
                    Lanjut Belanja →
                </a>
            </div>

            @if (session('success'))
                <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-100">
                    {{ session('error') }}
                </div>
            @endif

            @php $items = $cart->items ?? collect(); @endphp

            @if ($items->isEmpty())
                <div class="text-center py-32 glass-panel rounded-[2rem]">
                    <div class="w-24 h-24 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-display font-medium text-gray-900 mb-3">Keranjang masih kosong</h2>
                    <p class="text-gray-500 mb-8 font-light">Ayo tambahkan produk favoritmu.</p>
                    <a href="{{ route('products.index') }}"
                       class="bg-gray-900 text-white px-10 py-4 rounded-full text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300 shadow-xl shadow-gray-900/10">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10" id="cart-page">
                    <div class="lg:col-span-2 space-y-4" id="cart-items">
                        @foreach ($items as $item)
                            <x-cart-item :item="$item" />
                        @endforeach
                    </div>

                    <div class="lg:col-span-1">
                        <div class="glass-panel p-8 rounded-3xl sticky top-28 border border-white/60 shadow-lg shadow-rose-100/20">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-display font-medium text-gray-900">Ringkasan Pesanan</h3>
                                <form action="{{ route('cart.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-rose-500 uppercase tracking-widest hover:text-rose-600">
                                        Kosongkan
                                    </button>
                                </form>
                            </div>

                            <div class="space-y-3 text-sm text-gray-600 mb-6">
                                <div class="flex items-center justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-medium" id="cart-subtotal">Rp {{ number_format($cart->getSubtotal(), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Pengiriman</span>
                                    <span class="text-gray-400 text-xs">Dihitung saat checkout</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xl font-display font-medium text-gray-900 border-t border-gray-100 pt-4 mb-8">
                                <span>Total</span>
                                <span id="cart-total">Rp {{ number_format($cart->getTotal(), 0, ',', '.') }}</span>
                            </div>

                            @auth('web')
                                <a href="{{ route('checkout.form') }}"
                                   class="block w-full text-center bg-gray-900 text-white py-4 rounded-xl text-xs font-bold tracking-[0.2em] uppercase hover:bg-primary transition-all duration-300 shadow-xl shadow-gray-900/20">
                                    Lanjut Checkout
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="block w-full text-center bg-gray-900 text-white py-4 rounded-xl text-xs font-bold tracking-[0.2em] uppercase hover:bg-primary transition-all duration-300 shadow-xl shadow-gray-900/20">
                                    Login untuk Checkout
                                </a>
                            @endauth

                            <p class="text-[10px] text-gray-400 uppercase tracking-widest text-center mt-4">Ongkir dihitung berdasarkan alamat</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cartItems = document.getElementById('cart-items');
            const subtotalEl = document.getElementById('cart-subtotal');
            const totalEl = document.getElementById('cart-total');
            const numberFormat = new Intl.NumberFormat('id-ID');

            const updateTotals = (totals) => {
                if (! totals) return;
                if (subtotalEl) subtotalEl.textContent = `Rp ${numberFormat.format(totals.subtotal ?? 0)}`;
                if (totalEl) totalEl.textContent = `Rp ${numberFormat.format(totals.total ?? 0)}`;
            };

            const setButtonLoading = (button, isLoading, textWhenLoading = 'Memproses...') => {
                if (!button) return;
                if (isLoading) {
                    button.dataset.originalText = button.textContent;
                    button.textContent = textWhenLoading;
                    button.classList.add('opacity-60', 'cursor-not-allowed');
                    button.disabled = true;
                } else {
                    button.textContent = button.dataset.originalText ?? button.textContent;
                    button.classList.remove('opacity-60', 'cursor-not-allowed');
                    button.disabled = false;
                }
            };

            const handleUpdate = async (form) => {
                const quantityInput = form.querySelector('input[name="quantity"]');
                const quantity = parseInt(quantityInput?.value ?? '1', 10);
                if (! quantity || quantity < 1) return;

                const submitButton = form.querySelector('button[type="submit"]');
                setButtonLoading(submitButton, true, 'Menyimpan...');

                try {
                    const response = await axios.patch(form.action, { quantity });
                    updateTotals(response.data?.totals);
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                    window.showToast?.('Kuantitas diperbarui.');
                } catch (error) {
                    window.showToast?.(error?.response?.data?.message ?? 'Gagal memperbarui kuantitas.', 'error');
                } finally {
                    setButtonLoading(submitButton, false);
                }
            };

            const handleRemove = async (form) => {
                const submitButton = form.querySelector('button[type="submit"]');
                setButtonLoading(submitButton, true, 'Menghapus...');
                try {
                    const response = await axios.delete(form.action);
                    const itemRow = form.closest('.js-cart-item');
                    if (itemRow) {
                        itemRow.remove();
                    }
                    updateTotals(response.data?.totals);
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                    window.showToast?.('Item dihapus.');

                    const remainingItems = document.querySelectorAll('.js-cart-item').length;
                    if (remainingItems === 0) {
                        window.location.reload();
                    }
                } catch (error) {
                    window.showToast?.(error?.response?.data?.message ?? 'Gagal menghapus item.', 'error');
                } finally {
                    setButtonLoading(submitButton, false);
                }
            };

            if (cartItems) {
                cartItems.addEventListener('submit', (event) => {
                    const form = event.target;
                    if (form.classList.contains('js-cart-update')) {
                        event.preventDefault();
                        handleUpdate(form);
                    }

                    if (form.classList.contains('js-cart-remove')) {
                        event.preventDefault();
                        handleRemove(form);
                    }
                });
            }
        });
    </script>
@endsection
