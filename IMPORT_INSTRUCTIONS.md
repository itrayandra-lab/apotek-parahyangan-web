# Instruksi Import Database SQL

**File SQL:** `C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web\temp\apotek_parahyangan_db.sql`  
**Database:** `apotek_parahyangan_db`

---

## 🚀 Cara Import (Pilih salah satu)

### **Opsi 1: Command Line MySQL (Recommended)**

1. **Buka Command Prompt (CMD) sebagai Administrator**
2. **Jalankan command berikut:**

```cmd
cd "C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web"

mysql -u root -h 127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp\apotek_parahyangan_db.sql
```

3. **Verifikasi import:**

```cmd
mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SHOW TABLES;"
```

---

### **Opsi 2: Laravel Artisan Seeder**

1. **Buka Command Prompt di folder project**
2. **Jalankan seeder:**

```cmd
php artisan db:seed --class=ImportSqlSeeder
```

---

### **Opsi 3: phpMyAdmin (GUI)**

1. **Buka phpMyAdmin** di browser: `http://localhost/phpmyadmin`
2. **Login** dengan username `root` (tanpa password)
3. **Buat database baru:**
   - Klik "New" di sidebar kiri
   - Nama database: `apotek_parahyangan_db`
   - Collation: `utf8mb4_unicode_ci`
   - Klik "Create"
4. **Import SQL file:**
   - Pilih database `apotek_parahyangan_db`
   - Klik tab "Import"
   - Klik "Choose File" dan pilih: `C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web\temp\apotek_parahyangan_db.sql`
   - Klik "Go"

---

### **Opsi 4: MySQL Workbench**

1. **Buka MySQL Workbench**
2. **Connect ke MySQL server** (localhost:3306, user: root)
3. **Buat database:**
   ```sql
   CREATE DATABASE IF NOT EXISTS apotek_parahyangan_db 
   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. **Import SQL file:**
   - Menu: Server → Data Import
   - Select "Import from Self-Contained File"
   - Browse dan pilih file SQL
   - Target Schema: `apotek_parahyangan_db`
   - Klik "Start Import"

---

### **Opsi 5: Herd/Laravel Valet (Jika menggunakan Herd)**

1. **Buka terminal Herd**
2. **Jalankan:**

```bash
cd /path/to/apotek-parahyangan-web
mysql -u root apotek_parahyangan_db < temp/apotek_parahyangan_db.sql
```

---

## 🔍 Verifikasi Import

Setelah import berhasil, jalankan command berikut untuk verifikasi:

### **Cek Tabel:**

```sql
USE apotek_parahyangan_db;
SHOW TABLES;
```

### **Cek Data Sample:**

```sql
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_products FROM products;
SELECT COUNT(*) as total_medicines FROM medicines;
SELECT COUNT(*) as total_orders FROM orders;
```

### **Cek Struktur Tabel:**

```sql
DESCRIBE users;
DESCRIBE products;
DESCRIBE medicines;
DESCRIBE orders;
```

---

## 📋 Tabel yang Seharusnya Ada

Setelah import berhasil, database harus memiliki tabel-tabel berikut:

### **Core Tables:**
- `users` - User accounts
- `password_reset_tokens` - Password resets
- `sessions` - User sessions
- `cache` - Cache storage
- `jobs` - Queue jobs
- `failed_jobs` - Failed jobs

### **Location Tables:**
- `provinces` - Indonesian provinces
- `cities` - Indonesian cities  
- `districts` - Indonesian districts
- `villages` - Indonesian villages

### **Product Management:**
- `categories` - Product categories
- `products` - Products
- `medicines` - Medicine products
- `medicine_categories` - Medicine categories
- `medicine_units` - Medicine units/pricing
- `suppliers` - Medicine suppliers
- `stock_batches` - Medicine stock batches

### **Content Management:**
- `articles` - Blog articles
- `article_categories` - Article categories
- `article_article_category` - Article-category pivot
- `tags` - Article tags
- `article_tag` - Article-tag pivot
- `sliders` - Homepage sliders
- `media` - Media files

### **E-commerce:**
- `carts` - Shopping carts
- `cart_items` - Cart items
- `orders` - Customer orders
- `order_items` - Order items
- `vouchers` - Discount vouchers
- `voucher_usages` - Voucher usage tracking
- `user_addresses` - Customer addresses

### **Prescription System:**
- `prescriptions` - Prescription uploads
- `prescription_orders` - Prescription orders
- `prescription_order_items` - Prescription order items

### **Chat & Communication:**
- `chat_sessions` - Chat sessions
- `chat_messages` - Chat messages
- `chatbot_configurations` - Chatbot settings
- `contact_messages` - Contact form messages

### **Settings:**
- `site_settings` - Site configuration
- `expert_quotes` - Expert testimonials

---

## ⚠️ Troubleshooting

### **Error: "Access denied for user 'root'"**

**Solusi:**
```cmd
mysql -u root -p -h 127.0.0.1
```
Masukkan password MySQL jika ada.

### **Error: "Unknown database 'apotek_parahyangan_db'"**

**Solusi:** Buat database terlebih dahulu:
```sql
CREATE DATABASE apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### **Error: "Table doesn't exist"**

**Solusi:** Import ulang dengan menghapus database terlebih dahulu:
```sql
DROP DATABASE IF EXISTS apotek_parahyangan_db;
CREATE DATABASE apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### **Error: "Foreign key constraint fails"**

**Solusi:** Disable foreign key checks sementara:
```sql
SET FOREIGN_KEY_CHECKS=0;
-- Import SQL file
SET FOREIGN_KEY_CHECKS=1;
```

---

## 🎯 Setelah Import Berhasil

1. **Update .env file** jika perlu:
   ```
   DB_DATABASE=apotek_parahyangan_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Clear cache Laravel:**
   ```cmd
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Test koneksi database:**
   ```cmd
   php artisan tinker
   ```
   Kemudian di tinker:
   ```php
   DB::connection()->getPdo();
   User::count();
   ```

4. **Jalankan aplikasi:**
   ```cmd
   php artisan serve
   ```

---

## 📞 Jika Masih Bermasalah

Jika masih ada masalah, coba:

1. **Restart MySQL service**
2. **Cek MySQL error log**
3. **Gunakan phpMyAdmin untuk import manual**
4. **Hubungi tim development**

---

**Status:** ✅ Instruksi siap digunakan  
**Rekomendasi:** Gunakan **Opsi 1 (Command Line)** atau **Opsi 3 (phpMyAdmin)**
