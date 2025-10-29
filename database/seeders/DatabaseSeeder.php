<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserType;
use App\Models\Users;
use App\Models\Localisation;
use App\Models\OrderStatut;
use App\Models\ItemType;
use App\Models\Item;
use App\Models\Payment;
use App\Models\EventType;
use App\Models\SpecialEvent;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Mot de passe par défaut pour tous les utilisateurs
        $defaultPassword = Hash::make('password');

        // 1. Types d'utilisateurs
        $userTypes = [
            ['user_type_id' => 'AD00000', 'user_type_name' => 'Admin'],
            ['user_type_id' => 'MG00000', 'user_type_name' => 'gérant'],
            ['user_type_id' => 'CH00000', 'user_type_name' => 'Chef'],
            ['user_type_id' => 'SE00000', 'user_type_name' => 'Serveur'],
            ['user_type_id' => 'LI00000', 'user_type_name' => 'Livreur'],
            ['user_type_id' => 'CA00000', 'user_type_name' => 'Caissier'],
            ['user_type_id' => 'CL00000', 'user_type_name' => 'Client'],
        ];

        foreach ($userTypes as $type) {
            UserType::create($type);
        }

        $this->command->info('✅ Types d\'utilisateurs créés');

        // 2. Utilisateurs
        $users = [
            // Admins
            ['user_id' => 'US_1_0001', 'user_type_id' => 'AD00000', 'first_name' => 'Mounjongue', 'last_name' => 'Katia', 'mail_adress' => 'katia.mounjongue@2029.ucac-icam.com', 'phone_number' => '237699943817'],
            
            // gérants
            ['user_id' => 'US_1_0002', 'user_type_id' => 'MG00000', 'first_name' => 'Justine', 'last_name' => 'Nebo', 'mail_adress' => 'justine-auriane.dzukou@2029.ucac-icam.com', 'phone_number' => '237677002001'],
            
            // Chefs
            ['user_id' => 'US_1_0003', 'user_type_id' => 'CH00000', 'first_name' => 'Mylena', 'last_name' => 'Chembou', 'mail_adress' => 'mylena.chembou@2029.ucac-icam.com', 'phone_number' => '237699458347'],
            ['user_id' => 'US_1_0004', 'user_type_id' => 'CH00000', 'first_name' => 'Eva', 'last_name' => 'Nguoghia', 'mail_adress' => 'gaia.nguoghia@2029.ucac-icam.com', 'phone_number' => '237657003935'],
            
            // Clients
            ['user_id' => 'US_1_0018', 'user_type_id' => 'CL00000', 'first_name' => 'Joel', 'last_name' => 'Bouyim', 'mail_adress' => 'joel.bouyim@2029.ucac-icam.com', 'phone_number' => '237698378587'],
            ['user_id' => 'US_1_0019', 'user_type_id' => 'CL00000', 'first_name' => 'Tomi', 'last_name' => 'Ngounou', 'mail_adress' => 'ariel.ngounou@2029.ucac-icam.com', 'phone_number' => '237688799832'],
        ];

        foreach ($users as $userData) {
            Users::create(array_merge($userData, [
                'password' => $defaultPassword,
                'inscription_date' => now(),
                'account_statut' => true,
            ]));
        }

        $this->command->info('✅ Utilisateurs créés');

        // 3. Localisations
        $localisations = [
            ['localisation_id' => 'LC00001', 'localisation_name' => 'Résidence Ucac-Icam', 'localisation_delevery_price' => 100.00],
            ['localisation_id' => 'LC00002', 'localisation_name' => 'Cité Bahamas', 'localisation_delevery_price' => 300.00],
            ['localisation_id' => 'LC00003', 'localisation_name' => 'Cité Commissariat', 'localisation_delevery_price' => 300.00],
            ['localisation_id' => 'LC00004', 'localisation_name' => 'Cité Tribunal', 'localisation_delevery_price' => 300.00],
            ['localisation_id' => 'LC00005', 'localisation_name' => 'Cité la Grâce', 'localisation_delevery_price' => 200.00],
        ];

        foreach ($localisations as $loc) {
            Localisation::create($loc);
        }

        $this->command->info('✅ Localisations créées');

        // 4. Statuts de commande
        $statuts = [
            ['order_statut_id' => 'OS000001', 'order_statut_name' => 'En attente'],
            ['order_statut_id' => 'OS000002', 'order_statut_name' => 'En préparation'],
            ['order_statut_id' => 'OS000003', 'order_statut_name' => 'En livraison'],
            ['order_statut_id' => 'OS000004', 'order_statut_name' => 'Livrée'],
            ['order_statut_id' => 'OS000005', 'order_statut_name' => 'Annulée'],
        ];

        foreach ($statuts as $statut) {
            OrderStatut::create($statut);
        }

        $this->command->info('✅ Statuts de commande créés');

        // 5. Types d'articles
        $itemTypes = [
            ['item_type_id' => 'IT00001', 'item_type_name' => 'Plats Camerounais'],
            ['item_type_id' => 'IT00002', 'item_type_name' => 'Plats Internationaux'],
            ['item_type_id' => 'IT00003', 'item_type_name' => 'Grillades'],
            ['item_type_id' => 'IT00004', 'item_type_name' => 'Boissons Gazeuses'],
            ['item_type_id' => 'IT00005', 'item_type_name' => 'Boissons Alcoolisées'],
            ['item_type_id' => 'IT00006', 'item_type_name' => 'Jus et Smoothies'],
            ['item_type_id' => 'IT00007', 'item_type_name' => 'Desserts'],
        ];

        foreach ($itemTypes as $type) {
            ItemType::create($type);
        }

        $this->command->info('✅ Types d\'articles créés');

        // 6. Articles du menu
        $items = [
            // Plats Camerounais
            ['item_id' => 'IM00001', 'item_type_id' => 'IT00001', 'name' => 'Ndolé Plantain Royal', 'description' => 'Le ndolé ici, c\'est pas le ndolé de chez tata oh ! Feuilles bien mixées, sauce bien graaave 😋', 'quantity' => 150, 'price' => 1200.00, 'image_link' => '/images/menu/ndole_plantain.jpg'],
            ['item_id' => 'IM00002', 'item_type_id' => 'IT00001', 'name' => 'Eru Complet', 'description' => 'Eru way only 😎 ! Feuilles vertes, waterleaf et un peu de magie 💚', 'quantity' => 120, 'price' => 1500.00, 'image_link' => '/images/menu/eru.jpg'],
            ['item_id' => 'IM00003', 'item_type_id' => 'IT00001', 'name' => 'Okok Salé', 'description' => 'Le okok salé qui fait fermer les yeux de plaisir 😍', 'quantity' => 100, 'price' => 1000.00, 'image_link' => '/images/menu/okok_sale.jpg'],
            ['item_id' => 'IM00004', 'item_type_id' => 'IT00001', 'name' => 'Okok Sucré', 'description' => 'La version sucrée qui met tout le monde d\'accord 😋', 'quantity' => 100, 'price' => 1000.00, 'image_link' => '/images/menu/okok_sucre.jpg'],
            ['item_id' => 'IM00005', 'item_type_id' => 'IT00001', 'name' => 'Koki', 'description' => 'Koki bien jaune, bien parfumé et plein de vibes 😋', 'quantity' => 80, 'price' => 800.00, 'image_link' => '/images/menu/koki.jpg'],
            
            // Plats Internationaux
            ['item_id' => 'IM00009', 'item_type_id' => 'IT00002', 'name' => 'Spaghetti Bolognaise', 'description' => 'Spaghetti qui groove 🍝. Sauce tomate bien chargée', 'quantity' => 100, 'price' => 1200.00, 'image_link' => '/images/menu/spag_bolo.jpg'],
            ['item_id' => 'IM00010', 'item_type_id' => 'IT00002', 'name' => 'Riz Cantonais', 'description' => 'Riz sauté version pro avec légumes croquants 🍚', 'quantity' => 95, 'price' => 1300.00, 'image_link' => '/images/menu/riz_cantonais.jpg'],
            
            // Grillades
            ['item_id' => 'IM00013', 'item_type_id' => 'IT00003', 'name' => 'Poulet Braisé Complet', 'description' => 'Poulet braisé qui fait K.O. tes papilles 😋', 'quantity' => 60, 'price' => 2500.00, 'image_link' => '/images/menu/poulet_braise.jpg'],
            ['item_id' => 'IM00014', 'item_type_id' => 'IT00003', 'name' => 'Poulet DG', 'description' => 'Le boss des plats camer 🔥 Poulet + plantain + sauce royale', 'quantity' => 50, 'price' => 3000.00, 'image_link' => '/images/menu/poulet_dg.jpg'],
            ['item_id' => 'IM00015', 'item_type_id' => 'IT00003', 'name' => 'Poulet Pané', 'description' => 'Poulet croustillant dehors, tendre dedans 🔥', 'quantity' => 70, 'price' => 2000.00, 'image_link' => '/images/menu/poulet_pane.jpg'],
            
            // Boissons Gazeuses
            ['item_id' => 'IM00019', 'item_type_id' => 'IT00004', 'name' => 'Coca-Cola 33cl', 'description' => 'La légende des boissons gazeuses 🥤', 'quantity' => 300, 'price' => 500.00, 'image_link' => '/images/menu/coca.jpg'],
            ['item_id' => 'IM00020', 'item_type_id' => 'IT00004', 'name' => 'Top Pamplemousse 33cl', 'description' => 'Goût sucré-acidulé qui réveille 😋', 'quantity' => 250, 'price' => 400.00, 'image_link' => '/images/menu/top_pamplemousse.jpg'],
            ['item_id' => 'IM00021', 'item_type_id' => 'IT00004', 'name' => 'Malta Guinness 33cl', 'description' => 'Boisson maltée sans alcool 💪🏾', 'quantity' => 200, 'price' => 600.00, 'image_link' => '/images/menu/malta.jpg'],
            
            // Boissons Alcoolisées
            ['item_id' => 'IM00027', 'item_type_id' => 'IT00005', 'name' => 'Castel Beer 60cl', 'description' => 'La bière des grands moments 🍺', 'quantity' => 150, 'price' => 1000.00, 'image_link' => '/images/menu/castel.jpg'],
            ['item_id' => 'IM00028', 'item_type_id' => 'IT00005', 'name' => '33 Export 60cl', 'description' => 'Classique et fière 🇨🇲', 'quantity' => 140, 'price' => 1000.00, 'image_link' => '/images/menu/33export.jpg'],
            
            // Jus
            ['item_id' => 'IM00032', 'item_type_id' => 'IT00006', 'name' => 'Jus d\'Orange Pressé', 'description' => 'Jus 100% naturel pressé minute 🍊', 'quantity' => 100, 'price' => 800.00, 'image_link' => '/images/menu/jus_orange.jpg'],
            ['item_id' => 'IM00033', 'item_type_id' => 'IT00006', 'name' => 'Jus de Bissap', 'description' => 'Bissap rouge intense, désaltérant 💜', 'quantity' => 120, 'price' => 600.00, 'image_link' => '/images/menu/bissap.jpg'],
            
            // Desserts
            ['item_id' => 'IM00037', 'item_type_id' => 'IT00007', 'name' => 'Beignets Haricots (5pcs)', 'description' => 'Beignets croustillants tout chauds ! 😋', 'quantity' => 150, 'price' => 500.00, 'image_link' => '/images/menu/beignets.jpg'],
            ['item_id' => 'IM00038', 'item_type_id' => 'IT00007', 'name' => 'Puff-Puff (6pcs)', 'description' => 'Boules dorées et sucrées qui fondent 🍩', 'quantity' => 140, 'price' => 400.00, 'image_link' => '/images/menu/puffpuff.jpg'],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        $this->command->info('✅ Articles du menu créés');

        // 7. Méthodes de paiement
        $payments = [
            ['payment_method_id' => 'PM00001', 'method_name' => 'Orange Money'],
            ['payment_method_id' => 'PM00002', 'method_name' => 'MTN Mobile Money'],
            ['payment_method_id' => 'PM00003', 'method_name' => 'Espèces'],
            ['payment_method_id' => 'PM00004', 'method_name' => 'Express Union'],
            ['payment_method_id' => 'PM00005', 'method_name' => 'Carte Bancaire Visa'],
            ['payment_method_id' => 'PM00006', 'method_name' => 'Carte Bancaire Mastercard'],
        ];

        foreach ($payments as $payment) {
            Payment::create($payment);
        }

        $this->command->info('✅ Méthodes de paiement créées');

        // 8. Types d'événements
        $eventTypes = [
            ['event_type_id' => 'EV00001', 'event_type_name' => 'Promotion'],
            ['event_type_id' => 'EV00002', 'event_type_name' => 'Soirée Spéciale'],
            ['event_type_id' => 'EV00003', 'event_type_name' => 'Jeux Concours'],
            ['event_type_id' => 'EV00004', 'event_type_name' => 'Anniversaire'],
            ['event_type_id' => 'EV00005', 'event_type_name' => 'Menu du Jour'],
        ];

        foreach ($eventTypes as $eventType) {
            EventType::create($eventType);
        }

        $this->command->info('✅ Types d\'événements créés');

        // 9. Événements spéciaux
        $events = [
            [
                'event_id' => 'SE00001',
                'event_type_id' => 'EV00001',
                'event_name' => 'Promo Rentrée 2025',
                'event_starting_date' => '2025-01-15',
                'event_ending_date' => '2025-01-31',
                'event_description' => 'Réduction de 20% sur tous les plats camerounais pour bien démarrer l\'année !'
            ],
            [
                'event_id' => 'SE00002',
                'event_type_id' => 'EV00001',
                'event_name' => 'Happy Hour Boissons',
                'event_starting_date' => '2025-01-20',
                'event_ending_date' => '2025-12-31',
                'event_description' => 'Toutes les boissons à -30% de 16h à 18h du lundi au vendredi'
            ],
            [
                'event_id' => 'SE00003',
                'event_type_id' => 'EV00003',
                'event_name' => 'Gagne ton Repas Gratuit',
                'event_starting_date' => '2025-02-01',
                'event_ending_date' => '2025-02-14',
                'event_description' => 'Commande 5 fois et gagne un repas gratuit pour la Saint-Valentin !'
            ],
        ];

        foreach ($events as $event) {
            SpecialEvent::create($event);
        }

        $this->command->info('✅ Événements spéciaux créés');

        $this->command->info('');
        $this->command->info('🎉 Base de données peuplée avec succès !');
        $this->command->info('');
        $this->command->info('📊 Résumé:');
        $this->command->info('   - Types d\'utilisateurs: ' . UserType::count());
        $this->command->info('   - Utilisateurs: ' . Users::count());
        $this->command->info('   - Localisations: ' . Localisation::count());
        $this->command->info('   - Statuts de commande: ' . OrderStatut::count());
        $this->command->info('   - Types d\'articles: ' . ItemType::count());
        $this->command->info('   - Articles: ' . Item::count());
        $this->command->info('   - Méthodes de paiement: ' . Payment::count());
        $this->command->info('   - Types d\'événements: ' . EventType::count());
        $this->command->info('   - Événements: ' . SpecialEvent::count());
        $this->command->info('');
        $this->command->info('🔑 Identifiants de connexion:');
        $this->command->info('   Admin: katia.mounjongue@2029.ucac-icam.com / @Katia2029');
        $this->command->info('   gérant: justine-auriane.dzukou@2029.ucac-icam.com/ @Auriane2029');
        $this->command->info('   Chef: mylena.chembou@2029.ucac-icam.com /  @Bernadette2029');
        $this->command->info('   Client: joel.bouyim@2029.ucac-icam.com /  @Joel2029');
        $this->command->info('   Client: ariel.ngounou@2029.ucac-icam.com /  @Tomi2029');

        
    }
}