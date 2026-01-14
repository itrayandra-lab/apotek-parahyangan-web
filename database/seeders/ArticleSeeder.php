<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        
        if (!$admin) {
            echo "No user found to assign as author. Please run UserSeeder first.\n";
            return;
        }

        // Create categories if they don't exist
        $categories = [
            ['name' => 'Kesehatan Umum', 'description' => 'Informasi kesehatan sehari-hari'],
            ['name' => 'Tips Obat', 'description' => 'Cara penggunaan obat yang benar'],
            ['name' => 'Gaya Hidup Sehat', 'description' => 'Tips menjaga kebugaran tubuh'],
        ];

        foreach ($categories as $cat) {
            ArticleCategory::firstOrCreate(['name' => $cat['name']], [
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description']
            ]);
        }

        $allCategories = ArticleCategory::all();

        $articles = [
            [
                'title' => 'Cara Mengenali Batuk Kering dan Batuk Berdahak',
                'excerpt' => 'Mengetahui perbedaan jenis batuk sangat penting untuk menentukan jenis obat yang tepat bagi kesembuhan Anda.',
                'body' => 'Batuk adalah respon alami tubuh untuk mengeluarkan benda asing dari saluran pernapasan. Namun, seringkali kita bingung membedakan antara batuk kering dan batuk berdahak. Batuk berdahak biasanya ditandai dengan adanya lendir atau mukus yang diproduksi oleh saluran pernapasan, seringkali muncul akibat infeksi virus seperti flu atau sinusitis. Sebaliknya, batuk kering terasa gatal di tenggorokan tanpa adanya lendir yang keluar. Memilih obat yang tepat seperti ekspektoran untuk batuk berdahak atau antitusif untuk batuk kering adalah kunci utama. Jangan lupa untuk selalu berkonsultasi dengan apoteker di Apotek Parahyangan untuk mendapatkan rekomendasi produk terbaik sesuai kondisi gejala yang Anda alami saat ini agar pemulihan berjalan lebih cepat dan efektif.',
            ],
            [
                'title' => 'Pentingnya Menjaga Konsumsi Vitamin C Setiap Hari',
                'excerpt' => 'Vitamin C memiliki peran vital dalam meningkatkan sistem imun tubuh, terutama di masa perubahan cuaca yang ekstrem.',
                'body' => 'Di tengah cuaca yang tidak menentu, menjaga kesehatan menjadi tantangan tersendiri bagi kita semua. Salah satu cara paling efektif adalah dengan memastikan asupan Vitamin C tercukupi setiap hari. Vitamin C bukan hanya sekadar suplemen, tetapi antioksidan kuat yang melindungi sel-sel tubuh dari kerusakan radikal bebas. Selain itu, Vitamin C berperan dalam pembentukan kolagen yang penting untuk kesehatan kulit dan jaringan ikat. Bagi Anda yang aktif bekerja, konsumsi suplemen Vitamin C berkualitas tinggi sangat disarankan untuk menjaga stamina agar tetap prima sepanjang hari. Kunjungi Apotek Parahyangan untuk menemukan berbagai pilihan vitamin, mulai dari tablet hisap, kapsul, hingga varian yang ramah bagi lambung sensitif Anda.',
            ],
            [
                'title' => 'Waspadai Bahaya Mengonsumsi Obat Tanpa Resep Dokter',
                'excerpt' => 'Penggunaan obat keras secara sembarangan dapat memicu efek samping serius hingga resistensi antibiotik dalam tubuh.',
                'body' => 'Obat-obatan diciptakan untuk menyembuhkan, namun jika digunakan secara salah, dampaknya bisa membahayakan jiwa. Masih banyak masyarakat yang membeli obat antibiotik atau obat keras tanpa resep resmi, padahal hal ini sangat berisiko memicu resistensi bakteri. Ketika bakteri menjadi resisten, penyakit yang seharusnya mudah disembuhkan akan menjadi jauh lebih sulit diobati di kemudian hari. Sangat penting bagi kita untuk memahami klasifikasi obat, seperti logo lingkaran merah (obat keras) yang wajib menggunakan resep dokter. Di Apotek Parahyangan, apoteker kami selalu siap memberikan edukasi mengenai cara penggunaan obat yang aman, dosis yang tepat, serta informasi mengenai interaksi obat agar manfaat kesehatan yang didapatkan maksimal tanpa mengesampingkan faktor keamanan jangka panjang.',
            ],
            [
                'title' => 'Tips Menjaga Kualitas Tidur Agar Jauh Dari Penyakit',
                'excerpt' => 'Tidur yang cukup adalah fondasi kesehatan selain pola makan dan olahraga. Temukan cara memperbaiki ritme tidur Anda di sini.',
                'body' => 'Banyak orang meremehkan waktu tidur, padahal saat tidurlah tubuh melakukan regenerasi sel secara besar-besaran dan memperkuat sistem pertahanan tubuh. Kurang tidur secara kronis dapat meningkatkan risiko penyakit jantung, diabetes, hingga gangguan kesehatan mental seperti depresi. Untuk mendapatkan kualitas tidur yang baik, mulailah dengan menciptakan lingkungan kamar yang tenang, sejuk, dan gelap. Hindari penggunaan perangkat elektronik setidaknya satu jam sebelum memejamkan mata agar produksi melatonin tidak terganggu. Jika Anda sering mengalami insomnia, penggunaan teh herbal atau suplemen melatonin ringan terkadang bisa membantu, namun pastikan untuk tetap berkonsultasi terlebih dahulu agar tidak menimbulkan ketergantungan. Hidup sehat dimulai dari istirahat yang berkualitas dan hati yang tenang setiap malam.',
            ],
        ];

        foreach ($articles as $data) {
            $article = Article::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'excerpt' => $data['excerpt'],
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'views_count' => rand(10, 100),
            ]);

            // Assign body via RichText
            $article->body = $data['body'];
            
            // Randomly attach 2 categories
            $article->categories()->attach(
                $allCategories->random(rand(1, 2))->pluck('id')->toArray()
            );
        }

        echo "Successfully created 4 sample articles.\n";
    }
}
