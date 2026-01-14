@php
    $supportEmail = \App\Models\SiteSetting::getValue('contact.support_email', 'support@beautylatory.com');
    $siteUrl = config('app.url');
    $siteName = \App\Models\SiteSetting::getValue('general.site_name', 'Beautylatory');
    $businessAddress = \App\Models\SiteSetting::getValue('contact.address', '');
    $contactPhone = \App\Models\SiteSetting::getValue('contact.phone', '');
@endphp

@extends('layouts.app')

@section('title', 'Kebijakan Pengiriman - ' . $siteName)

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
                <h1 class="text-4xl font-display font-medium text-gray-900">Kebijakan Pengiriman</h1>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 prose prose-gray max-w-none">
                <p class="text-sm text-gray-500 mb-6">Terakhir diperbarui: {{ now()->format('d F Y') }}</p>

                <h2 class="text-xl font-display font-medium text-gray-900 mt-0">Informasi Pengiriman {{ $siteName }}</h2>
                <p>Kami berkomitmen untuk mengirimkan pesanan Anda dengan aman dan tepat waktu. Berikut adalah informasi lengkap mengenai proses pengiriman kami.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">1. Area Pengiriman</h3>
                <p>{{ $siteName }} melayani pengiriman ke seluruh wilayah Indonesia, termasuk:</p>
                <ul>
                    <li>Pulau Jawa, Sumatera, Kalimantan, Sulawesi</li>
                    <li>Bali, Nusa Tenggara, Maluku, Papua</li>
                    <li>Daerah terpencil (dengan waktu pengiriman lebih lama)</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">2. Jasa Ekspedisi</h3>
                <p>Kami bekerja sama dengan berbagai jasa ekspedisi terpercaya:</p>
                <ul>
                    <li><strong>JNE:</strong> REG, YES, OKE</li>
                    <li><strong>J&T Express:</strong> Regular, Express</li>
                    <li><strong>SiCepat:</strong> REG, BEST</li>
                    <li><strong>Pos Indonesia:</strong> Kilat Khusus</li>
                </ul>
                <p>Pilihan ekspedisi akan ditampilkan saat checkout beserta estimasi biaya dan waktu pengiriman.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">3. Estimasi Waktu Pengiriman</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border border-gray-200 px-4 py-2 text-left">Wilayah</th>
                                <th class="border border-gray-200 px-4 py-2 text-left">Estimasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Jabodetabek</td>
                                <td class="border border-gray-200 px-4 py-2">1-2 hari kerja</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Pulau Jawa</td>
                                <td class="border border-gray-200 px-4 py-2">2-4 hari kerja</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Sumatera, Bali</td>
                                <td class="border border-gray-200 px-4 py-2">3-5 hari kerja</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">Kalimantan, Sulawesi</td>
                                <td class="border border-gray-200 px-4 py-2">4-7 hari kerja</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2">NTT, NTB, Maluku, Papua</td>
                                <td class="border border-gray-200 px-4 py-2">7-14 hari kerja</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-gray-500 mt-2">*Estimasi dihitung dari tanggal pengiriman, bukan tanggal pemesanan. Waktu dapat berbeda tergantung kondisi.</p>

                <h3 class="text-lg font-display font-medium text-gray-900">4. Proses Pemesanan hingga Pengiriman</h3>
                <ol>
                    <li><strong>Pemesanan:</strong> Anda melakukan checkout dan pembayaran.</li>
                    <li><strong>Konfirmasi:</strong> Kami memverifikasi pembayaran (1x24 jam).</li>
                    <li><strong>Pengemasan:</strong> Pesanan dikemas dengan aman (1-2 hari kerja).</li>
                    <li><strong>Pengiriman:</strong> Paket diserahkan ke ekspedisi dan Anda menerima nomor resi.</li>
                    <li><strong>Pelacakan:</strong> Lacak paket via website ekspedisi dengan nomor resi.</li>
                    <li><strong>Penerimaan:</strong> Paket tiba di alamat Anda.</li>
                </ol>

                <h3 class="text-lg font-display font-medium text-gray-900">5. Biaya Pengiriman</h3>
                <ul>
                    <li>Ongkir dihitung berdasarkan berat dan tujuan pengiriman.</li>
                    <li>Biaya ditampilkan secara real-time saat checkout.</li>
                    <li>Promo gratis ongkir dapat berlaku untuk minimum pembelian tertentu.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">6. Pengemasan</h3>
                <p>Semua produk dikemas dengan standar keamanan tinggi:</p>
                <ul>
                    <li>Bubble wrap untuk perlindungan produk.</li>
                    <li>Kardus tebal untuk keamanan tambahan.</li>
                    <li>Label "FRAGILE" untuk produk yang mudah pecah.</li>
                    <li>Segel keamanan untuk menjamin keaslian produk.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">7. Pelacakan Pesanan</h3>
                <p>Setelah pesanan dikirim, Anda akan menerima:</p>
                <ul>
                    <li>Email/WhatsApp konfirmasi pengiriman.</li>
                    <li>Nomor resi untuk pelacakan.</li>
                    <li>Link untuk tracking di halaman pesanan Anda.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">8. Pengiriman Gagal</h3>
                <p>Jika pengiriman gagal karena:</p>
                <ul>
                    <li><strong>Alamat tidak lengkap/salah:</strong> Hubungi customer service untuk update alamat.</li>
                    <li><strong>Penerima tidak di tempat:</strong> Kurir akan mencoba ulang atau paket disimpan di drop point.</li>
                    <li><strong>Ditolak penerima:</strong> Paket dikembalikan, biaya pengiriman ulang ditanggung pembeli.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">9. Kerusakan saat Pengiriman</h3>
                <p>Jika produk rusak saat pengiriman:</p>
                <ul>
                    <li>Dokumentasikan dengan foto/video saat menerima paket.</li>
                    <li>Hubungi kami dalam 1x24 jam setelah penerimaan.</li>
                    <li>Kami akan memproses klaim atau penggantian.</li>
                    <li>Lihat <a href="{{ route('refund') }}" class="text-primary hover:underline">Kebijakan Pengembalian</a> untuk detail.</li>
                </ul>

                <h3 class="text-lg font-display font-medium text-gray-900">10. Hubungi Kami</h3>
                <p>Untuk pertanyaan mengenai pengiriman:</p>
                <ul>
                    <li>Email: <a href="mailto:{{ $supportEmail }}" class="text-primary hover:underline">{{ $supportEmail }}</a></li>
                    @if($contactPhone)
                    <li>WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactPhone) }}" class="text-primary hover:underline" target="_blank">{{ $contactPhone }}</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection
