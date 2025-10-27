<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderStatutController;
use App\Http\Controllers\LocalisationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SponsorshipController;
use App\Http\Controllers\LoyaltyPointController;
use App\Http\Controllers\VIPmembersController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemTypeController;
use App\Http\Controllers\Admin\ItemStatsController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\EventTypeController;
use App\Http\Controllers\Admin\SpecialEventController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\RestaurantHoursController;


/*
|--------------------------------------------------------------------------
| 1. AUTHENTIFICATION (PUBLIQUE)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    // Routes publiques
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Routes protégées (authentifié)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
        Route::get('/permissions', [AuthController::class, 'getPermissions'])->name('auth.permissions');
    });
});

/*
|--------------------------------------------------------------------------
| 2. ROUTES PUBLIQUES (SANS AUTHENTIFICATION)
|--------------------------------------------------------------------------
*/

// Menu public
Route::prefix('menu')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/featured', [MenuController::class, 'getFeaturedItems'])->name('menu.featured');
    Route::get('/by-category', [MenuController::class, 'getMenuByCategory'])->name('menu.by-category');
    Route::get('/search', [MenuController::class, 'advancedSearch'])->name('menu.search');
    Route::get('/items/{item_id}', [MenuController::class, 'show'])->name('menu.show');
});

// Horaires du restaurant
Route::prefix('restaurant')->group(function () {
    Route::get('/hours', [RestaurantHoursController::class, 'getOpeningHours'])->name('restaurant.hours');
    Route::get('/status', [RestaurantHoursController::class, 'checkIfOpen'])->name('restaurant.status');
    Route::get('/hours/today', [RestaurantHoursController::class, 'getTodayHours'])->name('restaurant.today');
    Route::get('/special-closings', [RestaurantHoursController::class, 'getSpecialClosings'])->name('restaurant.closings');
});

// Contacts support (public)
Route::get('/support/contacts', [ComplaintController::class, 'getSupportContacts'])->name('support.contacts');

// Localisations publiques
Route::get('/localisations', [LocalisationController::class, 'index'])->name('localisations.index');

// Moyens de paiement publics
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

/*
|--------------------------------------------------------------------------
| 3. ROUTES CLIENTS AUTHENTIFIÉS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // ========== COMMANDES ==========
    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'createOrder'])->name('orders.create');
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order_id}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/{order_id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/{order_id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
        Route::delete('/{order_id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    // ========== PAIEMENTS ==========
    Route::prefix('payment')->group(function () {
        Route::post('/{order_id}/initiate', [OrderController::class, 'initiatePayment'])->name('payment.initiate');
        Route::post('/callback/{order_id}', [OrderController::class, 'paymentCallback'])->name('payment.callback');
        Route::get('/{order_id}/status', [OrderController::class, 'checkPaymentStatus'])->name('payment.status');
        Route::post('/validate-order-time', [RestaurantHoursController::class, 'validateOrderTime'])->name('payment.validate-time');
    });

    // ========== PARRAINAGE ==========
    Route::prefix('sponsorship')->group(function () {
        Route::post('/generate/{user_id}', [SponsorshipController::class, 'generateSponsorshipCode'])->name('sponsorship.generate');
        Route::post('/use', [SponsorshipController::class, 'useSponsorshipCode'])->name('sponsorship.use');
        Route::get('/{user_id}/godchildren', [SponsorshipController::class, 'getGodchildren'])->name('sponsorship.godchildren');
        Route::get('/{user_id}/sponsor', [SponsorshipController::class, 'getSponsor'])->name('sponsorship.sponsor');
    });

    // ========== POINTS DE FIDÉLITÉ ==========
    Route::prefix('loyalty')->group(function () {
        Route::get('/{user_id}/balance', [LoyaltyPointController::class, 'getBalance'])->name('loyalty.balance');
        Route::post('/order/{order_id}/calculate', [LoyaltyPointController::class, 'calculateOrderPoints'])->name('loyalty.calculate');
        Route::post('/redeem', [LoyaltyPointController::class, 'redeemPoints'])->name('loyalty.redeem');
        Route::get('/leaderboard', [LoyaltyPointController::class, 'getLeaderboard'])->name('loyalty.leaderboard');
    });

    // ========== RÉCLAMATIONS ==========
    Route::prefix('complaints')->group(function () {
        Route::post('/', [ComplaintController::class, 'createComplaint'])->name('complaints.create');
        Route::get('/user/{user_id}', [ComplaintController::class, 'getUserComplaints'])->name('complaints.user');
        Route::get('/{complaint_id}', [ComplaintController::class, 'show'])->name('complaints.show');
    });

    // ========== PROFIL UTILISATEUR ==========
    Route::prefix('profile')->group(function () {
        Route::get('/', [UsersController::class, 'show'])->name('profile.show');
        Route::put('/', [UsersController::class, 'update'])->name('profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| 4. ESPACE ADMINISTRATEUR
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    
    // ========== GESTION DES UTILISATEURS ==========
    Route::prefix('users')->group(function () {
        Route::get('/statistics', [UsersController::class, 'statistics'])->name('admin.users.statistics');
        Route::get('/employees', [UsersController::class, 'getEmployees'])->name('admin.users.employees');
        Route::get('/clients', [UsersController::class, 'getClients'])->name('admin.users.clients');
        Route::get('/', [UsersController::class, 'index'])->name('admin.users.index');
        Route::post('/', [UsersController::class, 'store'])->name('admin.users.store');
        Route::get('/{id}', [UsersController::class, 'show'])->name('admin.users.show');
        Route::put('/{id}', [UsersController::class, 'update'])->name('admin.users.update');
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('admin.users.destroy');
        Route::patch('/{id}/toggle-status', [UsersController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::patch('/{id}/change-role', [UsersController::class, 'changeRole'])->name('admin.users.change-role');
    });

    // ========== TYPES D'UTILISATEURS ==========
    Route::prefix('user-types')->group(function () {
        Route::get('/', [UserTypeController::class, 'index'])->name('admin.user-types.index');
        Route::post('/', [UserTypeController::class, 'store'])->name('admin.user-types.store');
        Route::get('/{id}', [UserTypeController::class, 'show'])->name('admin.user-types.show');
        Route::put('/{id}', [UserTypeController::class, 'update'])->name('admin.user-types.update');
        Route::delete('/{id}', [UserTypeController::class, 'destroy'])->name('admin.user-types.destroy');
        Route::get('/{id}/statistics', [UserTypeController::class, 'statistics'])->name('admin.user-types.stats');
    });

    // ========== GESTION DU MENU ==========
    Route::prefix('menu')->group(function () {
        // Items
        Route::get('/', [MenuController::class, 'index'])->name('admin.menu.index');
        Route::post('/items', [MenuController::class, 'addItem'])->name('admin.menu.items.create');
        Route::get('/items/{item_id}', [MenuController::class, 'show'])->name('admin.menu.items.show');
        Route::put('/items/{item_id}', [MenuController::class, 'updateItem'])->name('admin.menu.items.update');
        Route::delete('/items/{item_id}', [MenuController::class, 'deleteItem'])->name('admin.menu.items.delete');
        Route::patch('/items/{item_id}/toggle-availability', [MenuController::class, 'toggleAvailability'])->name('admin.menu.items.toggle-availability');
        Route::patch('/items/{item_id}/stock', [MenuController::class, 'updateStock'])->name('admin.menu.items.stock');
        Route::patch('/items/{item_id}/toggle-featured', [MenuController::class, 'toggleFeatured'])->name('admin.menu.items.toggle-featured');
        Route::post('/items/{item_id}/duplicate', [MenuController::class, 'duplicateItem'])->name('admin.menu.items.duplicate');
        Route::get('/export', [MenuController::class, 'exportMenu'])->name('admin.menu.export');
        
        // Types d'items
        Route::get('/item-types', [ItemTypeController::class, 'index'])->name('admin.menu.item-types.index');
        Route::post('/item-types', [ItemTypeController::class, 'store'])->name('admin.menu.item-types.store');
        Route::get('/item-types/{id}', [ItemTypeController::class, 'show'])->name('admin.menu.item-types.show');
        Route::put('/item-types/{id}', [ItemTypeController::class, 'update'])->name('admin.menu.item-types.update');
        Route::delete('/item-types/{id}', [ItemTypeController::class, 'destroy'])->name('admin.menu.item-types.destroy');
    });

    // ========== STATISTIQUES ==========
    Route::prefix('statistics')->group(function () {
        Route::get('/items/most-ordered', [ItemStatsController::class, 'getMostOrderedItems'])->name('admin.stats.most-ordered');
        Route::get('/items/{item_id}/stats', [ItemStatsController::class, 'getItemOrderStats'])->name('admin.stats.item-details');
        Route::get('/users', [UsersController::class, 'statistics'])->name('admin.stats.users');
        Route::get('/payments', [PaymentController::class, 'statistics'])->name('admin.stats.payments');
    });

    // ========== COMMANDES ==========
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/{order_id}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::patch('/{order_id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        Route::post('/{order_id}/cancel', [OrderController::class, 'cancelOrder'])->name('admin.orders.cancel');
    });

    // ========== STATUTS DE COMMANDE ==========
    Route::prefix('order-statuts')->group(function () {
        Route::get('/', [OrderStatutController::class, 'index'])->name('admin.order-statuts.index');
        Route::post('/', [OrderStatutController::class, 'store'])->name('admin.order-statuts.store');
        Route::get('/{id}', [OrderStatutController::class, 'show'])->name('admin.order-statuts.show');
    });

    // ========== LOCALISATIONS ==========
    Route::prefix('localisations')->group(function () {
        Route::get('/', [LocalisationController::class, 'index'])->name('admin.localisations.index');
        Route::post('/', [LocalisationController::class, 'store'])->name('admin.localisations.store');
        Route::get('/{id}', [LocalisationController::class, 'show'])->name('admin.localisations.show');
        Route::put('/{id}', [LocalisationController::class, 'update'])->name('admin.localisations.update');
        Route::delete('/{id}', [LocalisationController::class, 'destroy'])->name('admin.localisations.destroy');
    });

    // ========== MOYENS DE PAIEMENT ==========
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::post('/', [PaymentController::class, 'store'])->name('admin.payments.store');
        Route::get('/statistics', [PaymentController::class, 'statistics'])->name('admin.payments.statistics');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('admin.payments.show');
        Route::put('/{id}', [PaymentController::class, 'update'])->name('admin.payments.update');
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');
    });

    // ========== FACTURES ==========
    Route::prefix('bills')->group(function () {
        Route::get('/', [BillController::class, 'index'])->name('admin.bills.index');
        Route::get('/{id}', [BillController::class, 'show'])->name('admin.bills.show');
    });

    // ========== ÉVÉNEMENTS ==========
    Route::prefix('events')->group(function () {
        // Types d'événements
        Route::get('/types', [EventTypeController::class, 'index'])->name('admin.event-types.index');
        Route::post('/types', [EventTypeController::class, 'store'])->name('admin.event-types.store');
        Route::get('/types/{id}', [EventTypeController::class, 'show'])->name('admin.event-types.show');
        Route::put('/types/{id}', [EventTypeController::class, 'update'])->name('admin.event-types.update');
        Route::delete('/types/{id}', [EventTypeController::class, 'destroy'])->name('admin.event-types.destroy');
        
        // Événements spéciaux
        Route::get('/', [SpecialEventController::class, 'index'])->name('admin.events.index');
        Route::post('/', [SpecialEventController::class, 'store'])->name('admin.events.store');
        Route::get('/{id}', [SpecialEventController::class, 'show'])->name('admin.events.show');
        Route::put('/{id}', [SpecialEventController::class, 'update'])->name('admin.events.update');
        Route::delete('/{id}', [SpecialEventController::class, 'destroy'])->name('admin.events.destroy');
    });

    // ========== HORAIRES DU RESTAURANT ==========
    Route::prefix('restaurant')->group(function () {
        Route::post('/hours', [RestaurantHoursController::class, 'setOpeningHours'])->name('admin.restaurant.hours.set');
        Route::post('/hours/weekly', [RestaurantHoursController::class, 'setWeeklyHours'])->name('admin.restaurant.hours.weekly');
        Route::post('/special-closings', [RestaurantHoursController::class, 'addSpecialClosing'])->name('admin.restaurant.closings.add');
        Route::delete('/special-closings/{closing_id}', [RestaurantHoursController::class, 'deleteSpecialClosing'])->name('admin.restaurant.closings.delete');
    });

    // ========== RÉCLAMATIONS ==========
    Route::prefix('complaints')->group(function () {
        Route::get('/', [ComplaintController::class, 'getAllComplaints'])->name('admin.complaints.index');
        Route::get('/statistics', [ComplaintController::class, 'getStatistics'])->name('admin.complaints.statistics');
        Route::get('/{complaint_id}', [ComplaintController::class, 'show'])->name('admin.complaints.show');
        Route::patch('/{complaint_id}/resolve', [ComplaintController::class, 'resolveComplaint'])->name('admin.complaints.resolve');
        Route::delete('/{complaint_id}', [ComplaintController::class, 'deleteComplaint'])->name('admin.complaints.delete');
    });

    // ========== MEMBRES VIP ==========
    Route::prefix('vip-members')->group(function () {
        Route::get('/', [VIPmembersController::class, 'index'])->name('admin.vip.index');
        Route::post('/', [VIPmembersController::class, 'store'])->name('admin.vip.store');
        Route::get('/{id}', [VIPmembersController::class, 'show'])->name('admin.vip.show');
        Route::put('/{id}', [VIPmembersController::class, 'update'])->name('admin.vip.update');
        Route::delete('/{id}', [VIPmembersController::class, 'destroy'])->name('admin.vip.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| 5. ESPACE MANAGER
|--------------------------------------------------------------------------
*/

Route::prefix('manager')->middleware(['auth:sanctum', 'role:Manager'])->group(function () {
    
    // Supervision des commandes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('manager.orders.index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('manager.orders.show');
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('manager.orders.update-status');
    });

    // Gestion des employés (consultation et création)
    Route::prefix('employees')->group(function () {
        Route::get('/', [UsersController::class, 'getEmployees'])->name('manager.employees.index');
        Route::post('/', [UsersController::class, 'store'])->name('manager.employees.store');
        Route::get('/{id}', [UsersController::class, 'show'])->name('manager.employees.show');
    });

    // Statistiques
    Route::get('/statistics/dashboard', [ItemStatsController::class, 'getMostOrderedItems'])->name('manager.stats.dashboard');
});

/*
|--------------------------------------------------------------------------
| 6. ESPACE EMPLOYÉ
|--------------------------------------------------------------------------
*/

Route::prefix('employee')->middleware(['auth:sanctum'])->group(function () {
    
    // Gestion des commandes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('employee.orders.index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('employee.orders.show');
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->middleware('permission:update-order-status')->name('employee.orders.update-status');
    });

    // Menu (consultation par tous, modification par Chef)
    Route::prefix('menu')->group(function () {
        Route::get('/items', [MenuController::class, 'index'])->name('employee.menu.index');
        Route::patch('/items/{id}/availability', [MenuController::class, 'toggleAvailability'])->middleware(['role:Chef'])->name('employee.menu.toggle-availability');
    });
});