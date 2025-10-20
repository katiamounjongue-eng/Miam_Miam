<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\UserType; // Assurez-vous d'avoir ce Modèle


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            // Note: user_id sera géré par un Trait ou le Seeder si non-incrémenté
            'user_type_id' => UserType::where('user_type_name', 'Student')->first()?->user_type_id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            // Hachage sécurisé du mot de passe 
            'user_password' => Hash::make('password'), 
            'mail_adress' => $this->faker->unique()->safeEmail(),
            // Correction: Utilisation d'un format string pour le numéro de téléphone
            'phone_number' => '237' . $this->faker->unique()->randomNumber(8, true), 
            'inscription_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'account_statut' => true,
            'loyalty_points' => $this->faker->numberBetween(0, 5000), // Ajout des points de fidélité
        ];
    }
    
    // État pour créer un Admin facilement
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type_id' => UserType::where('user_type_name', 'Admin')->first()?->user_type_id,
                'mail_adress' => 'admin@zeducspace.com',
            ];
        });
    }

    // État pour créer un Employé facilement
    public function employee()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type_id' => UserType::where('user_type_name', 'Employee')->first()?->user_type_id,
            ];
        });
    }
}
