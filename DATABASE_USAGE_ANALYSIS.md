# Analisis Penggunaan Database - Apotek Parahyangan

**Tanggal Analisis:** 20 Januari 2026  
**Total Tabel:** 38+ tabel

---

## 📊 RINGKASAN TABEL YANG BELUM DIGUNAKAN

### ❌ TABEL YANG TIDAK DIGUNAKAN SAMA SEKALI

#### 1. **`article_tag` (Pivot Table)**
- **Status:** ❌ Tidak digunakan
- **Tujuan:** Relasi many-to-many antara articles dan tags
- **Alasan:** Sistem menggunakan `article_categories` sebagai pengganti, tidak ada implementasi tag untuk articles
- **Rekomendasi:** Hapus atau implementasikan fitur tagging untuk articles

#### 2. **`rich_texts` (Tabel Orphan)**
- **Status:** ❌ Tidak digunakan
- **Tujuan:** Menyimpan rich text content dari package `tonysm/rich-text-laravel`
- **Alasan:** Package diinstall tapi tidak digunakan di aplikasi
- **Rekomendasi:** Hapus jika tidak ada rencana menggunakan rich text editor

#### 3. **`cache` & `cache_locks` (Laravel Framework)**
- **Status:** ⚠️ Jarang digunakan
- **Tujuan:** Cache storage (default Laravel)
- **Alasan:** Aplikasi menggunakan default cache driver (file/database)
- **Rekomendasi:** Gunakan untuk caching query results atau session data

#### 4. **`jobs` & `job_batches` & `failed_jobs` (Queue System)**
- **Status:** ⚠️ Jarang digunakan
- **Tujuan:** Queue jobs untuk background processing
- **Alasan:** Aplikasi belum mengimplementasikan queue jobs
- **Rekomendasi:** Gunakan untuk email notifications, image processing, atau long-running tasks

---

## ✅ TABEL YANG DIGUNAKAN DENGAN BAIK

### **Core System (8 tabel)**
- ✅ `users` - Digunakan untuk auth, roles, dan relationships
- ✅ `password_reset_tokens` - Digunakan untuk password reset
- ✅ `sessions` - Digunakan untuk session management
- ✅ `provinces`, `cities`, `districts`, `villages` - Digunakan untuk shipping address

### **Product Management (4 tabel)**
- ✅ `categories` - Digunakan untuk product categories
- ✅ `products` - Digunakan untuk regular products (non-medicines)
- ✅ `sliders` - Digunakan untuk homepage sliders
- ✅ `media` - Digunakan untuk product images, article images, medicine images

### **Content Management (5 tabel)**
- ✅ `tags` - Digunakan untuk article tags
- ✅ `articles` - Digunakan untuk blog articles
- ✅ `article_categories` - Digunakan untuk article categories
- ✅ `article_article_category` - Digunakan untuk article-category relationships

### **Shopping & Orders (6 tabel)**
- ✅ `carts` - Digunakan untuk shopping carts
- ✅ `cart_items` - Digunakan untuk items dalam cart
- ✅ `orders` - Digunakan untuk pesanan reguler
- ✅ `order_items` - Digunakan untuk items dalam order
- ✅ `vouchers` - Digunakan untuk discount codes
- ✅ `voucher_usages` - Digunakan untuk tracking voucher usage

### **User Management (1 tabel)**
- ✅ `user_addresses` - Digunakan untuk saved addresses

### **Chat & Chatbot (3 tabel)**
- ✅ `chat_sessions` - Digunakan untuk chat sessions
- ✅ `chat_messages` - Digunakan untuk chat messages
- ✅ `chatbot_configurations` - Digunakan untuk chatbot settings

### **Testimonials & Settings (2 tabel)**
- ✅ `expert_quotes` - Digunakan untuk expert testimonials di homepage
- ✅ `site_settings` - Digunakan untuk site configuration

### **Contact & Feedback (1 tabel)**
- ✅ `contact_messages` - Digunakan untuk contact form submissions

### **Prescription Management (3 tabel)**
- ✅ `prescriptions` - Digunakan untuk prescription uploads
- ✅ `prescription_orders` - Digunakan untuk orders dari prescriptions
- ✅ `prescription_order_items` - Digunakan untuk items dalam prescription orders

### **Medicines (5 tabel)**
- ✅ `medicines` - Digunakan untuk pharmacy products
- ✅ `medicine_categories` - Digunakan untuk medicine categories
- ✅ `medicine_units` - Digunakan untuk medicine pricing/units
- ✅ `stock_batches` - Digunakan untuk medicine stock tracking
- ✅ `suppliers` - Digunakan untuk medicine suppliers

---

## 📋 DETAIL TABEL YANG BELUM DIGUNAKAN

### 1. **`article_tag` (Pivot Table)**

**Struktur:**
```sql
CREATE TABLE article_tag (
    id BIGINT PRIMARY KEY,
    article_id BIGINT FOREIGN KEY,
    tag_id BIGINT FOREIGN KEY,
    timestamps
)
```

**Penggunaan Saat Ini:**
- Tidak ada query yang menggunakan tabel ini
- Tidak ada relationship yang mengakses tabel ini

**Alasan Tidak Digunakan:**
- Sistem menggunakan `article_categories` untuk kategorisasi articles
- Tags dibuat tapi tidak diintegrasikan dengan articles

**Opsi:**
1. **Hapus:** Jika tidak ada rencana menggunakan tags untuk articles
2. **Implementasikan:** Tambahkan fitur tagging untuk articles di admin panel

**Rekomendasi:** Hapus jika tidak ada rencana, karena menambah kompleksitas database

---

### 2. **`rich_texts` (Tabel Orphan)**

**Struktur:**
```sql
CREATE TABLE rich_texts (
    id BIGINT PRIMARY KEY,
    body LONGTEXT,
    timestamps
)
```

**Penggunaan Saat Ini:**
- Package `tonysm/rich-text-laravel` diinstall tapi tidak digunakan
- Tidak ada model yang menggunakan rich text

**Alasan Tidak Digunakan:**
- Aplikasi menggunakan Trix editor untuk articles (bukan rich-text-laravel)
- Tidak ada implementasi rich text di tempat lain

**Opsi:**
1. **Hapus:** Uninstall package dan drop tabel
2. **Implementasikan:** Gunakan untuk article content atau product descriptions

**Rekomendasi:** Hapus package dan tabel jika tidak ada rencana menggunakan

---

### 3. **`cache` & `cache_locks`**

**Struktur:**
```sql
CREATE TABLE cache (
    key VARCHAR PRIMARY KEY,
    value LONGTEXT,
    expiration INT
)

CREATE TABLE cache_locks (
    key VARCHAR PRIMARY KEY,
    owner VARCHAR,
    expiration INT
)
```

**Penggunaan Saat Ini:**
- Jarang digunakan
- Default Laravel cache driver

**Alasan Jarang Digunakan:**
- Aplikasi belum mengimplementasikan caching strategy
- Tidak ada query caching atau session caching

**Opsi:**
1. **Gunakan untuk Query Caching:**
   ```php
   $products = Cache::remember('featured_products', 3600, function () {
       return Product::where('is_featured', true)->get();
   });
   ```

2. **Gunakan untuk Session Caching:**
   ```php
   Cache::put('user_cart_' . auth()->id(), $cart, 86400);
   ```

**Rekomendasi:** Implementasikan caching untuk query yang sering diakses (featured products, categories, etc.)

---

### 4. **`jobs`, `job_batches`, `failed_jobs` (Queue System)**

**Struktur:**
```sql
CREATE TABLE jobs (
    id BIGINT PRIMARY KEY,
    queue VARCHAR,
    payload LONGTEXT,
    attempts INT,
    reserved_at INT,
    available_at INT,
    created_at INT
)

CREATE TABLE job_batches (
    id VARCHAR PRIMARY KEY,
    name VARCHAR,
    total_jobs INT,
    pending_jobs INT,
    failed_jobs INT,
    failed_job_ids LONGTEXT,
    options LONGTEXT,
    cancelled_at INT,
    created_at INT,
    finished_at INT
)

CREATE TABLE failed_jobs (
    id BIGINT PRIMARY KEY,
    uuid VARCHAR UNIQUE,
    connection VARCHAR,
    queue VARCHAR,
    payload LONGTEXT,
    exception LONGTEXT,
    failed_at TIMESTAMP
)
```

**Penggunaan Saat Ini:**
- Tidak ada queue jobs yang diimplementasikan
- Semua proses berjalan synchronously

**Alasan Tidak Digunakan:**
- Aplikasi belum membutuhkan background processing
- Email notifications dikirim synchronously

**Opsi:**
1. **Implementasikan untuk Email Notifications:**
   ```php
   Mail::queue(new OrderPaidMail($order));
   ```

2. **Implementasikan untuk Image Processing:**
   ```php
   ProcessProductImage::dispatch($product);
   ```

3. **Implementasikan untuk Prescription Verification Notifications:**
   ```php
   SendPrescriptionVerificationNotification::dispatch($prescription);
   ```

**Rekomendasi:** Implementasikan queue jobs untuk:
- Email notifications (order confirmation, payment status)
- Image processing (product images, prescription images)
- WhatsApp notifications untuk prescription orders
- Prescription expiry handling

---

## 🎯 REKOMENDASI AKSI

### **Prioritas Tinggi (Lakukan Sekarang)**

1. **Hapus `article_tag` & `tags` jika tidak digunakan**
   - Cek apakah ada rencana menggunakan tags
   - Jika tidak, hapus untuk menyederhanakan database
   - Migration: `php artisan make:migration drop_article_tag_table`

2. **Hapus `rich_texts` & uninstall package**
   - Aplikasi menggunakan Trix editor, bukan rich-text-laravel
   - Uninstall: `composer remove tonysm/rich-text-laravel`
   - Drop tabel: `php artisan make:migration drop_rich_texts_table`

### **Prioritas Sedang (Lakukan Dalam 1-2 Minggu)**

3. **Implementasikan Queue Jobs**
   - Setup queue driver (database atau redis)
   - Implementasikan email notifications sebagai queued jobs
   - Implementasikan WhatsApp notifications untuk prescriptions
   - Benefit: Aplikasi lebih responsif, user tidak perlu menunggu email terkirim

4. **Implementasikan Caching Strategy**
   - Cache featured products (update setiap 1 jam)
   - Cache medicine categories (update setiap 1 jam)
   - Cache article categories (update setiap 1 jam)
   - Benefit: Performa lebih cepat, database load berkurang

### **Prioritas Rendah (Optional)**

5. **Implementasikan Article Tagging**
   - Jika ada rencana untuk article discovery/filtering
   - Tambahkan UI untuk manage tags di admin panel
   - Benefit: Better content organization

---

## 📈 STATISTIK DATABASE

| Kategori | Jumlah | Status |
|----------|--------|--------|
| Tabel Aktif | 34 | ✅ Digunakan |
| Tabel Tidak Digunakan | 2 | ❌ Bisa dihapus |
| Tabel Jarang Digunakan | 4 | ⚠️ Bisa dioptimalkan |
| **Total** | **40** | - |

---

## 🔍 TABEL YANG PERLU DIPERHATIKAN

### **Tabel dengan Potensi Masalah:**

1. **`media` (Spatie MediaLibrary)**
   - Status: ✅ Digunakan
   - Catatan: Pastikan cleanup old media files secara berkala
   - Rekomendasi: Implementasikan scheduled task untuk delete orphaned media

2. **`prescriptions` & `prescription_orders`**
   - Status: ✅ Digunakan
   - Catatan: Pastikan ada backup untuk data resep
   - Rekomendasi: Implementasikan archiving untuk resep lama

3. **`orders` & `order_items`**
   - Status: ✅ Digunakan
   - Catatan: Tabel ini akan terus bertambah
   - Rekomendasi: Implementasikan indexing yang baik untuk query performance

---

## 📝 KESIMPULAN

Aplikasi Apotek Parahyangan memiliki database yang **well-structured** dengan:
- ✅ 34 tabel yang digunakan dengan baik
- ❌ 2 tabel yang tidak digunakan (bisa dihapus)
- ⚠️ 4 tabel yang jarang digunakan (bisa dioptimalkan)

**Rekomendasi utama:**
1. Hapus `article_tag` dan `rich_texts` untuk menyederhanakan database
2. Implementasikan queue jobs untuk email & WhatsApp notifications
3. Implementasikan caching strategy untuk performa lebih baik
4. Pastikan indexing yang baik untuk tabel besar (orders, prescriptions)

