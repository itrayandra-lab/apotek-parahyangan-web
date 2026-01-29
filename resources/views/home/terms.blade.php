@php
    $supportEmail = \App\Models\SiteSetting::getValue('contact.support_email', 'support@Apotek Parahyangan Suite.com');
    $siteUrl = config('app.url');
    $siteName = \App\Models\SiteSetting::getValue('general.site_name', 'Apotek Parahyangan Suite');
    $businessAddress = \App\Models\SiteSetting::getValue('contact.address', '');
    $contactPhone = \App\Models\SiteSetting::getValue('contact.phone', '');
@endphp

@extends('layouts.app')

@section('title', 'Syarat & Ketentuan - ' . $siteName)

@section('content')
    <div class="pt-28 pb-20 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 md:px-8 max-w-4xl">
            <div class="mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold tracking-widest text-gray-400 hover:text-primary mb-6 transition-colors uppercase group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
                <p class="text-xs font-bold tracking-widest text-primary uppercase mb-2">Kebijakan</p>
                <h1 class="text-4xl font-display font-medium text-gray-900">Syarat & Ketentuan</h1>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 prose prose-gray max-w-none">
                <p class="text-sm text-gray-500 mb-6">Terakhir diperbarui: {{ now()->format('d F Y') }}</p>

                <h2 class="text-xl font-display font-medium text-gray-900 mt-0">Selamat Datang di {{ $siteName }}</h2>
                <p>Dengan mengakses dan menggunakan website {{ $siteName }} ({{ $siteUrl }}), Anda menyetujui untuk terikat dengan syarat dan ketentuan berikut. Harap baca dengan seksama sebelum menggunakan layanan kami.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">1. Definisi</h3>
                <ul>
                    <li><strong>"Website"</strong> merujuk pada {{ $siteUrl }} dan seluruh halaman yang terkait.</li>
                    <li><strong>"Kami"</strong> merujuk pada {{ $siteName }}.</li>
                    <li><strong>"Pengguna"</strong> merujuk pada setiap individu yang mengakses atau menggunakan Website.</li>
                    <li><strong>"Produk"</strong> merujuk pada barang yang dijual melalui Website.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">2. Penggunaan Website</h3>
                <p>Dengan menggunakan Website ini, Anda menyatakan bahwa:</p>
                <ul>
                    <li>Anda berusia minimal 18 tahun atau memiliki izin dari orang tua/wali.</li>
                    <li>Informasi yang Anda berikan adalah akurat dan lengkap.</li>
                    <li>Anda bertanggung jawab untuk menjaga kerahasiaan akun Anda.</li>
                    <li>Anda tidak akan menggunakan Website untuk tujuan ilegal atau melanggar hukum.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">3. Akun Pengguna</h3>
                <p>Untuk melakukan pembelian, Anda perlu membuat akun dengan informasi yang valid. Anda bertanggung jawab atas:</p>
                <ul>
                    <li>Keamanan password dan akun Anda.</li>
                    <li>Semua aktivitas yang terjadi di akun Anda.</li>
                    <li>Memberitahu kami segera jika terjadi penggunaan tidak sah.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">4. Pemesanan dan Pembayaran</h3>
                <ul>
                    <li>Harga produk dapat berubah sewaktu-waktu tanpa pemberitahuan.</li>
                    <li>Pesanan dianggap sah setelah pembayaran dikonfirmasi.</li>
                    <li>Kami berhak membatalkan pesanan jika terjadi kesalahan harga atau stok.</li>
                    <li>Pembayaran diproses melalui payment gateway yang aman (Midtrans).</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">5. Hak Kekayaan Intelektual</h3>
                <p>Seluruh konten di Website ini, termasuk teks, gambar, logo, dan desain, adalah milik {{ $siteName }} dan dilindungi oleh hukum hak cipta Indonesia. Dilarang:</p>
                <ul>
                    <li>Menyalin, memodifikasi, atau mendistribusikan konten tanpa izin.</li>
                    <li>Menggunakan konten untuk tujuan komersial tanpa persetujuan tertulis.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">6. Batasan Tanggung Jawab</h3>
                <p>{{ $siteName }} tidak bertanggung jawab atas:</p>
                <ul>
                    <li>Kerugian tidak langsung akibat penggunaan Website.</li>
                    <li>Gangguan atau error pada Website yang di luar kendali kami.</li>
                    <li>Keterlambatan pengiriman akibat force majeure.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">7. Kebijakan Terkait</h3>
                <p>Syarat & Ketentuan ini harus dibaca bersama dengan:</p>
                <ul>
                    <li><a href="{{ route('privacy') }}" class="text-primary hover:underline">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('refund') }}" class="text-primary hover:underline">Kebijakan Pengembalian & Refund</a></li>
                    <li><a href="{{ route('delivery') }}" class="text-primary hover:underline">Kebijakan Pengiriman</a></li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">8. Perubahan Syarat & Ketentuan</h3>
                <p>Kami berhak mengubah Syarat & Ketentuan ini sewaktu-waktu. Perubahan akan berlaku segera setelah dipublikasikan di Website. Penggunaan berkelanjutan setelah perubahan berarti Anda menyetujui syarat yang baru.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">9. Hukum yang Berlaku</h3>
                <p>Syarat & Ketentuan ini diatur oleh dan ditafsirkan sesuai dengan hukum Republik Indonesia. Setiap sengketa akan diselesaikan melalui musyawarah, dan jika tidak tercapai kesepakatan, akan diselesaikan di Pengadilan Negeri yang berwenang.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">10. Hubungi Kami</h3>
                <p>Jika Anda memiliki pertanyaan mengenai Syarat & Ketentuan ini:</p>
                <ul>
                    <li>Email: <a href="mailto:{{ $supportEmail }}" class="text-primary hover:underline">{{ $supportEmail }}</a></li>
                    @if($contactPhone)
                    <li>WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactPhone) }}" class="text-primary hover:underline" target="_blank">{{ $contactPhone }}</a></li>
                    @endif
                    @if($businessAddress)
                    <li>Alamat: {{ $businessAddress }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection
