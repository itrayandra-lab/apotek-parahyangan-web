@php
    $supportEmail = \App\Models\SiteSetting::getValue('contact.support_email', 'support@beautylatory.com');
    $siteUrl = config('app.url');
    $siteName = \App\Models\SiteSetting::getValue('general.site_name', 'Beautylatory');
    $businessAddress = \App\Models\SiteSetting::getValue('contact.address', '');
    $contactPhone = \App\Models\SiteSetting::getValue('contact.phone', '');
@endphp

@extends('layouts.app')

@section('title', 'Kebijakan Privasi - ' . $siteName)

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
                <h1 class="text-4xl font-display font-medium text-gray-900">Kebijakan Privasi</h1>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 prose prose-gray max-w-none">
                <p class="text-sm text-gray-500 mb-6">Terakhir diperbarui: {{ now()->format('d F Y') }}</p>

                <h2 class="text-xl font-display font-medium text-gray-900 mt-0">Pendahuluan</h2>
                <p>{{ $siteName }} ("kami") berkomitmen untuk melindungi privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda saat menggunakan website kami di {{ $siteUrl }}.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">1. Informasi yang Kami Kumpulkan</h3>
                <p>Kami mengumpulkan informasi berikut saat Anda menggunakan layanan kami:</p>
                <h4 class="font-semibold text-gray-900">a. Informasi yang Anda Berikan:</h4>
                <ul>
                    <li>Nama lengkap</li>
                    <li>Alamat email</li>
                    <li>Nomor telepon</li>
                    <li>Alamat pengiriman</li>
                    <li>Informasi pembayaran (diproses secara aman oleh Midtrans)</li>
                </ul>
                <h4 class="font-semibold text-gray-900">b. Informasi yang Dikumpulkan Secara Otomatis:</h4>
                <ul>
                    <li>Alamat IP</li>
                    <li>Jenis browser dan perangkat</li>
                    <li>Halaman yang dikunjungi</li>
                    <li>Waktu dan tanggal kunjungan</li>
                    <li>Cookies dan teknologi serupa</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">2. Penggunaan Informasi</h3>
                <p>Informasi Anda digunakan untuk:</p>
                <ul>
                    <li>Memproses dan mengirimkan pesanan Anda.</li>
                    <li>Berkomunikasi mengenai pesanan, pengiriman, dan layanan.</li>
                    <li>Mengirimkan informasi promosi (dengan persetujuan Anda).</li>
                    <li>Meningkatkan layanan dan pengalaman pengguna.</li>
                    <li>Mencegah penipuan dan aktivitas ilegal.</li>
                    <li>Memenuhi kewajiban hukum.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">3. Pembagian Informasi</h3>
                <p>Kami tidak menjual atau menyewakan informasi pribadi Anda. Informasi hanya dibagikan kepada:</p>
                <ul>
                    <li><strong>Penyedia Layanan Pengiriman:</strong> Untuk mengirimkan pesanan Anda.</li>
                    <li><strong>Payment Gateway (Midtrans):</strong> Untuk memproses pembayaran secara aman.</li>
                    <li><strong>Pihak Berwenang:</strong> Jika diwajibkan oleh hukum.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">4. Keamanan Data</h3>
                <p>Kami menerapkan langkah-langkah keamanan untuk melindungi data Anda:</p>
                <ul>
                    <li>Enkripsi SSL/TLS untuk transmisi data.</li>
                    <li>Pembayaran diproses melalui Midtrans yang tersertifikasi PCI-DSS.</li>
                    <li>Akses terbatas hanya untuk karyawan yang membutuhkan.</li>
                    <li>Pemantauan keamanan secara berkala.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">5. Cookies</h3>
                <p>Website kami menggunakan cookies untuk:</p>
                <ul>
                    <li>Mengingat preferensi dan keranjang belanja Anda.</li>
                    <li>Menganalisis traffic website.</li>
                    <li>Meningkatkan fungsionalitas website.</li>
                </ul>
                <p>Anda dapat mengatur browser untuk menolak cookies, namun beberapa fitur website mungkin tidak berfungsi optimal.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">6. Hak Anda</h3>
                <p>Anda memiliki hak untuk:</p>
                <ul>
                    <li>Mengakses informasi pribadi yang kami simpan.</li>
                    <li>Memperbarui atau memperbaiki informasi Anda.</li>
                    <li>Meminta penghapusan data (dengan batasan tertentu).</li>
                    <li>Berhenti berlangganan dari komunikasi pemasaran.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">7. Penyimpanan Data</h3>
                <p>Kami menyimpan informasi Anda selama diperlukan untuk:</p>
                <ul>
                    <li>Menyediakan layanan kepada Anda.</li>
                    <li>Memenuhi kewajiban hukum dan pajak.</li>
                    <li>Menyelesaikan sengketa dan menegakkan perjanjian.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">8. Tautan ke Situs Lain</h3>
                <p>Website kami mungkin berisi tautan ke situs pihak ketiga. Kami tidak bertanggung jawab atas praktik privasi situs tersebut. Harap baca kebijakan privasi mereka sebelum memberikan informasi.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">9. Perubahan Kebijakan</h3>
                <p>Kami dapat memperbarui Kebijakan Privasi ini sewaktu-waktu. Perubahan akan dipublikasikan di halaman ini dengan tanggal pembaruan baru. Penggunaan berkelanjutan setelah perubahan berarti Anda menyetujui kebijakan yang baru.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">10. Hubungi Kami</h3>
                <p>Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini:</p>
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
