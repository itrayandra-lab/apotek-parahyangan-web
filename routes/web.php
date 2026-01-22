<?php

use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\ChatbotSettingsController;
use App\Http\Controllers\Admin\ExpertQuoteController as AdminExpertQuoteController;
use App\Http\Controllers\Admin\MedicineController as AdminMedicineController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PrescriptionAdminController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CheckoutControllerNew;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndonesiaRegionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

// Frontend routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/syarat-ketentuan', [HomeController::class, 'terms'])->name('terms');
Route::get('/kebijakan-privasi', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/kebijakan-pengembalian', [HomeController::class, 'refund'])->name('refund');
Route::get('/kebijakan-pengiriman', [HomeController::class, 'delivery'])->name('delivery');
Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');
Route::post('/kontak', [App\Http\Controllers\ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

// Product routes (Apotek Parahyangan Shop)
Route::get('/shop', [ProductController::class, 'guestIndex'])->name('products.index');
Route::get('/shop/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/shop/load-more', [ProductController::class, 'loadMore'])->name('products.load-more');

// Article routes
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::post('/articles/load-more', [ArticleController::class, 'loadMore'])->name('articles.load-more');
Route::get('/articles/category/{slug}', [ArticleController::class, 'showByCategory'])->name('articles.category');
Route::get('/article/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// Cart routes (guest + authenticated)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/add', [CartController::class, 'add'])->middleware('throttle:60,1')->name('cart.add');
Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{item}', [CartController::class, 'remove'])->name('cart.items.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout routes (auth only)
Route::middleware(['auth', 'customer.auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'form'])->name('checkout.form');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/pay-offline/{order}', [CheckoutController::class, 'payOffline'])->name('checkout.pay-offline');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/checkout/pending/{order}', [CheckoutController::class, 'pending'])->name('checkout.pending');

    Route::prefix('api/indonesia')->name('indonesia.')->group(function (): void {
        Route::get('/provinces', [IndonesiaRegionController::class, 'provinces'])->name('provinces');
        Route::get('/cities', [IndonesiaRegionController::class, 'cities'])->name('cities');
        Route::get('/districts', [IndonesiaRegionController::class, 'districts'])->name('districts');
        Route::get('/villages', [IndonesiaRegionController::class, 'villages'])->name('villages');
    });

    Route::prefix('api/shipping')->name('shipping.')->group(function (): void {
        Route::get('/search-destination', [ShippingController::class, 'searchDestination'])->name('search-destination');
        Route::post('/calculate-cost', [ShippingController::class, 'calculateCost'])->name('calculate-cost');
    });

    Route::prefix('api/voucher')->name('voucher.')->group(function (): void {
        Route::post('/apply', [VoucherController::class, 'apply'])->name('apply');
        Route::post('/remove', [VoucherController::class, 'remove'])->name('remove');
    });
});

Route::get('/checkout/error', [CheckoutController::class, 'error'])->name('checkout.error');

// Payment webhooks
Route::post('/payment/midtrans/notification', [PaymentWebhookController::class, 'midtrans'])->name('payment.midtrans.notification');

// Authentication routes (unified for admin & customer)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:30,1')->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:30,1')->name('register.post');
});

// Logout route (for both admin & customer) - check both guards
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:web,admin')
    ->name('logout');

// Customer routes
Route::middleware(['auth', 'customer.auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\CustomerController::class, 'index'])->name('customer.dashboard');
    Route::get('/profile', [App\Http\Controllers\CustomerController::class, 'showProfile'])->name('customer.profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\CustomerController::class, 'editProfile'])->name('customer.profile.edit');
    Route::put('/profile', [App\Http\Controllers\CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // User addresses
    Route::get('/addresses', [UserAddressController::class, 'index'])->name('addresses.index');
    Route::get('/api/addresses', [UserAddressController::class, 'list'])->name('addresses.list');
    Route::post('/api/addresses', [UserAddressController::class, 'store'])->name('addresses.store');
    Route::put('/api/addresses/{address}', [UserAddressController::class, 'update'])->name('addresses.update');
    Route::delete('/api/addresses/{address}', [UserAddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/api/addresses/{address}/default', [UserAddressController::class, 'setDefault'])->name('addresses.set-default');

    // Prescription routes
    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/upload', [PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/prescriptions/payment/{prescriptionOrder}', [PrescriptionController::class, 'payment'])->name('prescriptions.payment');
    Route::get('/prescription-orders/{prescriptionOrder}', [PrescriptionController::class, 'showOrder'])->name('prescriptions.order');
    
    // API for prescription status polling
    Route::get('/api/prescriptions/{prescription}/status', [PrescriptionController::class, 'status'])->name('api.prescriptions.status');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['admin.auth'])->group(function () {

    // Shared admin & content manager routes
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AdminController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [AdminController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    // Article routes
    Route::resource('articles', AdminArticleController::class);
    Route::post('articles/{article}/publish', [AdminArticleController::class, 'publish'])->name('articles.publish');
    Route::post('articles/{article}/unschedule', [AdminArticleController::class, 'unschedule'])->name('articles.unschedule');
    Route::resource('article-categories', ArticleCategoryController::class);
    Route::post('/attachments', [AttachmentController::class, 'store'])->name('attachments.store');

    // Admin-only routes
    Route::middleware('admin.auth:admin')->group(function () {
        Route::get('products/trash', [ProductController::class, 'trash'])->name('products.trash');
        Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('medicines', AdminMedicineController::class)->only(['index', 'edit', 'update']);
        Route::resource('categories', CategoryController::class);
        Route::resource('slider', SliderController::class);
        Route::resource('expert-quotes', AdminExpertQuoteController::class)->except(['show']);
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/mark-paid', [AdminOrderController::class, 'markPaid'])->name('orders.mark-paid');
        Route::get('orders/{order}/payment-callback', [AdminOrderController::class, 'paymentCallback'])->name('orders.payment-callback');
        Route::patch('orders/{order}/update-awb', [AdminOrderController::class, 'updateAwb'])->name('orders.update-awb');

        Route::get('chatbot-settings', [ChatbotSettingsController::class, 'index'])->name('chatbot.settings');
        Route::post('chatbot-settings', [ChatbotSettingsController::class, 'update'])->name('chatbot.settings.update');
        Route::post('chatbot-settings/test-webhook', [ChatbotSettingsController::class, 'testWebhook'])->name('chatbot.test-webhook');

        Route::get('site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
        Route::post('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');
        Route::post('site-settings/test-email', [SiteSettingController::class, 'testEmail'])->name('site-settings.test-email');

        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::resource('vouchers', AdminVoucherController::class)->except(['show']);

        Route::get('contact-messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::delete('contact-messages/{contactMessage}', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        // Prescription management routes
        Route::get('prescriptions', [PrescriptionAdminController::class, 'index'])->name('prescriptions.index');
        Route::get('prescriptions/{prescription}', [PrescriptionAdminController::class, 'show'])->name('prescriptions.show');
        Route::post('prescriptions/{prescription}/verify', [PrescriptionAdminController::class, 'verify'])->name('prescriptions.verify');
        Route::post('prescriptions/{prescription}/reject', [PrescriptionAdminController::class, 'reject'])->name('prescriptions.reject');
        Route::get('prescriptions/search/products', [PrescriptionAdminController::class, 'searchProducts'])->name('prescriptions.search-products');
        Route::get('prescriptions/orders/{order}/whatsapp', [PrescriptionAdminController::class, 'getWhatsAppLink'])->name('prescriptions.whatsapp');
        Route::post('prescriptions/orders/{order}/status', [PrescriptionAdminController::class, 'updateOrderStatus'])->name('prescriptions.update-order-status');
        Route::post('prescriptions/verify-qr', [PrescriptionAdminController::class, 'verifyQrCode'])->name('prescriptions.verify-qr');
    });
});

// Consultation Chat routes
Route::prefix('api/chat')
    ->group(function (): void {
        Route::get('/sync', [App\Http\Controllers\Api\ChatController::class, 'sync'])->name('chat.sync');
        Route::post('/send', [App\Http\Controllers\Api\ChatController::class, 'send'])->name('chat.send');
    });

// Chatbot routes (API-style under web stack)
Route::prefix('api/chatbot')
    ->middleware(['chatbot.access', 'throttle:30,1'])
    ->group(function (): void {
        Route::get('/status', [ChatbotController::class, 'status'])->name('chatbot.status');
        Route::get('/session', [ChatbotController::class, 'session'])->name('chatbot.session');
        Route::get('/history', [ChatbotController::class, 'history'])->name('chatbot.history');
        Route::post('/send', [ChatbotController::class, 'send'])->name('chatbot.send');
        Route::post('/reset', [ChatbotController::class, 'reset'])->name('chatbot.reset');
    });
