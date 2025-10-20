<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ItemController as AdminItemController; 
use App\Http\Controllers\Admin\ItemTypeController;
use App\Http\Controllers\Admin\EventTypeController;
use App\Http\Controllers\Admin\SpecialEventController as AdminSpecialEventController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\OrderHistoricController as AdminOrderHistoricController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderHistoricController; // Controller de base 


// Méthodes de paiement disponibles 
Route::get('payments', [PaymentController::class, 'index']);
Route::get('payments/{id}', [PaymentController::class, 'show']);

// Affichage du Menu et des Événements 
Route::get('menu', [AdminItemController::class, 'index']); 
Route::get('menu/{id}', [AdminItemController::class, 'show']); 

Route::get('events', [AdminSpecialEventController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    

    // Historique de l'étudiant connecté
    Route::get('historic', [OrderHistoricController::class, 'indexStudent']);
});



Route::middleware(['auth:sanctum', 'can:manage-operations'])->prefix('admin')->group(function () { 
    // Gestion du Menu 
    Route::apiResource('item-types', ItemTypeController::class);
    Route::apiResource('items', AdminItemController::class);

    // Gestion des Événements 
    Route::apiResource('event-types', EventTypeController::class);
    Route::apiResource('special-events', AdminSpecialEventController::class);

    // Suivi et Statistiques 
    Route::get('bills', [BillController::class, 'index']);
    Route::get('bills/{id}', [BillController::class, 'show']);
    Route::get('historic', [AdminOrderHistoricController::class, 'indexAdmin']);
});