<?php

use App\Http\Controllers\OrderController;

// Routes API REST complètes pour Order
Route::apiResource('orders', OrderController::class);

// Routes API REST complètes pour Users
use App\Http\Controllers\UsersController;
Route::apiResource('users', UsersController::class);

// Routes API REST complètes pour UserType
use App\Http\Controllers\UserTypeController;
Route::apiResource('user_type', UserTypeController::class);

// Routes API REST complètes pour VIP_members
use App\Http\Controllers\VIPmembersController;
Route::apiResource('VIP_members', VIPmembersController::class);

// Routes API REST complètes pour Localisation
use App\Http\Controllers\LocalisationController;
Route::apiResource('Localisation', LocalisationController::class);

// Routes API REST complètes pour order_statut
use App\Http\Controllers\OrderStatutController;
Route::apiResource('order_statut', OrderStatutController::class);

// Routes API REST complètes pour sponsorships
use App\Http\Controllers\SponsorshipController;
Route::apiResource('sponsorships', SponsorshipController::class);




