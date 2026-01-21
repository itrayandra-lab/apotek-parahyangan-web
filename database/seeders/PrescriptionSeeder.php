<?php

namespace Database\Seeders;

use App\Models\Prescription;
use App\Models\PrescriptionOrder;
use App\Models\PrescriptionOrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('prescriptions');

        // Get or create a test user with WhatsApp
        $user = User::where('role', 'user')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test Customer',
                'username' => 'testcustomer',
                'email' => 'customer@test.com',
                'password' => bcrypt('password'),
                'role' => 'user',
                'whatsapp' => '081234567890',
            ]);
        } elseif (!$user->whatsapp) {
            $user->update(['whatsapp' => '081234567890']);
        }

        // Create a pending prescription
        $pendingPrescription = Prescription::create([
            'user_id' => $user->id,
            'image_path' => 'prescriptions/sample_prescription_pending.jpg',
            'user_notes' => 'Mohon diproses segera. Saya membutuhkan obat untuk sakit kepala.',
            'status' => 'pending',
        ]);

        // Create a verified prescription with order
        $verifiedPrescription = Prescription::create([
            'user_id' => $user->id,
            'image_path' => 'prescriptions/sample_prescription_verified.jpg',
            'user_notes' => 'Resep untuk obat diabetes rutin.',
            'admin_notes' => 'Resep sudah diverifikasi. Silakan lakukan pembayaran.',
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => User::where('role', 'admin')->first()?->id,
        ]);

        // Create order for verified prescription
        $order = PrescriptionOrder::create([
            'user_id' => $user->id,
            'prescription_id' => $verifiedPrescription->id,
            'total_price' => 0,
            'payment_status' => 'unpaid',
            'pickup_status' => 'waiting',
        ]);

        // Add some products to the order (get first 3 products)
        $products = Product::take(3)->get();
        foreach ($products as $product) {
            PrescriptionOrderItem::create([
                'prescription_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => rand(1, 3),
                'price_at_purchase' => $product->discount_price ?? $product->price,
            ]);
        }

        // Calculate total
        $order->calculateTotal();

        // Create a rejected prescription
        Prescription::create([
            'user_id' => $user->id,
            'image_path' => 'prescriptions/sample_prescription_rejected.jpg',
            'user_notes' => 'Resep untuk antibiotik.',
            'admin_notes' => 'Resep tidak dapat dibaca dengan jelas. Mohon upload ulang dengan foto yang lebih jelas.',
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => User::where('role', 'admin')->first()?->id,
        ]);

        $this->command->info('✅ Created sample prescriptions for testing');
        $this->command->info('📧 Test user: customer@test.com / password');
        $this->command->info('📱 WhatsApp: 081234567890');
        $this->command->info('📋 Prescriptions created:');
        $this->command->info('   - 1 Pending prescription');
        $this->command->info('   - 1 Verified prescription with order');
        $this->command->info('   - 1 Rejected prescription');
    }
}
