<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ConversationController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Routes protégées (nécessitent un token JWT)
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Route de test pour vérifier le rôle de l'utilisateur connecté
    Route::get('user/profile', function () {
        return auth()->user();
    });

    // --- 3. Routes D'Administration (Seulement Rôle 'administrator') ---
    // Le middleware 'role:administrator' vérifie si l'utilisateur a ce rôle.
    Route::middleware('role:administrator')->prefix('admin')->group(function () {
        // Tâche 1 : Gestion des Employés
        Route::apiResource('employees', EmployeeController::class); // Créer, Modifier, Supprimer employés

        // Tâche 2 : Statistiques globales
        Route::get('stats/global', [StatsController::class, 'global']);
    });

    // --- 4. Routes Gérant (Rôles 'administrator' OU 'manager') ---
    // L'opérateur | signifie OU (l'un ou l'autre)
    Route::middleware('role:administrator|manager')->prefix('manager')->group(function () {
        // Le Gérant peut créer des employés et voir les commandes en temps réel.
        Route::get('orders/live', [OrderController::class, 'liveStatus']);
        // etc.
    });

    // --- 5. Routes Employé (Rôles 'administrator' OU 'manager' OU 'employee') ---
    Route::middleware('role:administrator|manager|employee')->prefix('employee')->group(function () {
        // Tâche : Valider les commandes
        Route::patch('orders/{order}/validate', [OrderController::class, 'validateOrder']);
        // Tâche : Mise à jour temporaire du menu
        Route::post('menu/update', [MenuController::class, 'temporaryUpdate']);
        // Historique des Commandes (pour la vue archivée)
        Route::get('history', [OrderController::class, 'history']); 
    

    });

    // --- 5.b Routes de gestion des commandes (Employé, Manager, Admin) ---
    Route::middleware('role:administrator|manager|employee')->prefix('orders')->group(function () {
        // Voir la liste des commandes (index)
        Route::get('/', [OrderController::class, 'index']);

        // Changer le statut d'une commande
        Route::patch('{order}/status', [OrderController::class, 'updateStatus']);
    });

    // --- 6. Routes Messagerie ---
    Route::prefix('conversations')->group(function () {
        // Admin/Employé : Liste des conversations (Maquette MESSAGES)
        Route::middleware('role:administrator|manager|employee')->get('/', [ConversationController::class, 'index']);

        // Tous : Afficher les messages d'une conversation (Autorisation via Policy ou logique interne)
        Route::get('{conversation}', [ConversationController::class, 'show']);

        // Tous : Poster un nouveau message (TEMPS RÉEL)
        Route::post('{conversation}/messages', [ConversationController::class, 'storeMessage']);

        // Étudiant : Créer une nouvelle conversation (initiation)
        Route::post('/', [ConversationController::class, 'startConversation']); 
    });

    // --- 6. Routes Étudiant (Tous les rôles, mais le rôle étudiant est implicite) ---
    // Ces routes sont accessibles à tous (y compris les admins, gérants, etc. s'ils ont besoin de commander).
    Route::get('menu', [MenuController::class, 'index']); // Voir le menu
    Route::post('orders', [OrderController::class, 'store']); // Passer une commande
});
    