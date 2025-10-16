<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Réinitialiser le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // 2. Créer les Rôles (idempotent)
    $adminRole = Role::firstOrCreate(['name' => 'administrator'], ['guard_name' => 'web']);
    $managerRole = Role::firstOrCreate(['name' => 'manager'], ['guard_name' => 'web']);
    $employeeRole = Role::firstOrCreate(['name' => 'employee'], ['guard_name' => 'web']);
    $studentRole = Role::firstOrCreate(['name' => 'student'], ['guard_name' => 'web']);
        
        // Nous pouvons définir des permissions plus tard, concentrons-nous sur les rôles pour l'instant.
        
        // 3. Créer des utilisateurs de test
        
        // A. Utilisateur ADMINISTRATEUR (Clé de voûte)
        $adminUser = User::firstOrCreate([
            'email' => 'admin@zeducspace.com'
        ], [
            'full_name' => 'Super Admin ZeDucSpace',
            'email' => 'admin@zeducspace.com',
            'password' => Hash::make('AdminSecure123'), // Mots de passe forts pour les tests
            'phone_number' => '0000000000',
            'localisation' => 'Siège Social',
            'role' => 'administrator', // Nous gardons le champ 'role' pour la clarté
        ]);
        $adminUser->assignRole($adminRole);

        // B. Utilisateur ÉTUDIANT (par défaut)
        // Les étudiants s'inscrivent via l'API, mais nous en créons un pour le test.
        $studentUser = User::firstOrCreate([
            'email' => 'student@test.com'
        ], [
            'full_name' => 'Etudiant Test',
            'email' => 'student@test.com',
            'password' => Hash::make('StudentSecure123'),
            'phone_number' => '1111111111',
            'localisation' => 'UCAC-ICAM',
            'role' => 'student',
        ]);
        $studentUser->assignRole($studentRole);

        // C. Utilisateur EMPLOYE (pour le test)
        $employeeUser = User::firstOrCreate([
            'email' => 'employee@test.com'
        ], [
            'full_name' => 'Employe Cuisine',
            'email' => 'employee@test.com',
            'password' => Hash::make('EmployeeSecure123'),
            'phone_number' => '2222222222',
            'localisation' => 'Cuisine',
            'role' => 'employee',
        ]);
        $employeeUser->assignRole($employeeRole);
    }
}