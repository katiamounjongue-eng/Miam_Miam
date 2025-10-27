<?php

namespace App\Http\Controllers;

use App\Models\Sponsorship;
use App\Models\Users;
use App\Models\Orders;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class SponsorshipController extends Controller
{
    /**
     * Générer un code de parrainage unique pour un utilisateur
     * 
     * @param string $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSponsorshipCode(string $userId)
    {
        $user = Users::findOrFail($userId);

        // Vérifier si l'utilisateur a déjà un code de parrainage
        $existingSponsorship = Sponsorship::where('student_id', $userId)->first();

        if ($existingSponsorship && $existingSponsorship->sponsorship_code) {
            return response()->json([
                'success' => true,
                'message' => 'Code de parrainage déjà existant.',
                'data' => [
                    'sponsorship_code' => $existingSponsorship->sponsorship_code,
                    'godchildren_count' => Sponsorship::where('godchild_id', $userId)->count(),
                ]
            ]);
        }

        // Générer un code unique
        do {
            $code = strtoupper(substr($user->first_name, 0, 3) . substr($user->last_name, 0, 3) . rand(1000, 9999));
        } while (Sponsorship::where('sponsorship_code', $code)->exists());

        // Créer ou mettre à jour l'entrée de parrainage
        if ($existingSponsorship) {
            $existingSponsorship->update(['sponsorship_code' => $code]);
            $sponsorship = $existingSponsorship;
        } else {
            $lastSponsorship = Sponsorship::orderBy('sponsorship_relation_id', 'desc')->first();
            $number = $lastSponsorship ? intval(substr($lastSponsorship->sponsorship_relation_id, 2)) + 1 : 1;
            $sponsorshipId = 'SR' . str_pad($number, 6, '0', STR_PAD_LEFT);

            $sponsorship = Sponsorship::create([
                'sponsorship_relation_id' => $sponsorshipId,
                'student_id' => $userId,
                'godchild_id' => null,
                'sponsorship_code' => $code,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code de parrainage généré avec succès.',
            'data' => [
                'sponsorship_relation_id' => $sponsorship->sponsorship_relation_id,
                'sponsorship_code' => $code,
                'share_message' => "Rejoignez-moi sur notre application restaurant avec mon code de parrainage : {$code} et gagnez des points de fidélité !",
            ]
        ], 201);
    }

    /**
     * Utiliser un code de parrainage (s'inscrire avec un parrain)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function useSponsorshipCode(Request $request)
    {
        $request->validate([
            'godchild_id' => 'required|exists:users,user_id',
            'sponsorship_code' => 'required|string|exists:sponsorships,sponsorship_code',
        ], [
            'sponsorship_code.exists' => 'Code de parrainage invalide.',
        ]);

        DB::beginTransaction();

        try {
            $godchildId = $request->godchild_id;
            $sponsorshipCode = $request->sponsorship_code;

            // Vérifier que l'utilisateur n'a pas déjà un parrain
            $existingRelation = Sponsorship::where('godchild_id', $godchildId)->first();

            if ($existingRelation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà utilisé un code de parrainage.'
                ], 400);
            }

            // Récupérer le parrain
            $sponsorRelation = Sponsorship::where('sponsorship_code', $sponsorshipCode)->first();
            $sponsorId = $sponsorRelation->student_id;

            // Vérifier qu'on ne se parraine pas soi-même
            if ($sponsorId === $godchildId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas utiliser votre propre code de parrainage.'
                ], 400);
            }

            // Créer la relation de parrainage
            $lastSponsorship = Sponsorship::orderBy('sponsorship_relation_id', 'desc')->first();
            $number = $lastSponsorship ? intval(substr($lastSponsorship->sponsorship_relation_id, 2)) + 1 : 1;
            $relationId = 'SR' . str_pad($number, 6, '0', STR_PAD_LEFT);

            $newRelation = Sponsorship::create([
                'sponsorship_relation_id' => $relationId,
                'student_id' => $sponsorId,
                'godchild_id' => $godchildId,
                'sponsorship_code' => null, // Le filleul n'a pas de code pour cette relation
            ]);

            // Attribuer des points bonus au parrain (500 points)
            $this->addLoyaltyPoints($sponsorId, 500, 'Bonus parrainage - Nouveau filleul');

            // Attribuer des points bonus au filleul (200 points)
            $this->addLoyaltyPoints($godchildId, 200, 'Bonus inscription avec code parrain');

            DB::commit();

            $sponsor = Users::find($sponsorId);
            $godchild = Users::find($godchildId);

            // Envoyer une notification au parrain
            $this->notifySponsor($sponsor, $godchild);

            return response()->json([
                'success' => true,
                'message' => 'Code de parrainage appliqué avec succès ! Vous avez reçu 200 points de fidélité.',
                'data' => [
                    'sponsorship_relation_id' => $newRelation->sponsorship_relation_id,
                    'sponsor' => [
                        'user_id' => $sponsor->user_id,
                        'name' => $sponsor->first_name . ' ' . $sponsor->last_name,
                    ],
                    'godchild' => [
                        'user_id' => $godchild->user_id,
                        'name' => $godchild->first_name . ' ' . $godchild->last_name,
                    ],
                    'bonus_points' => [
                        'sponsor_received' => 500,
                        'godchild_received' => 200,
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'application du code de parrainage.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les filleuls d'un parrain
     * 
     * @param string $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGodchildren(string $userId)
    {
        $user = Users::findOrFail($userId);

        $sponsorships = Sponsorship::where('student_id', $userId)
            ->whereNotNull('godchild_id')
            ->with('godchild')
            ->get();

        $godchildren = $sponsorships->map(function ($sponsorship) {
            $godchild = $sponsorship->godchild;
            $ordersCount = Orders::where('user_id', $godchild->user_id)->count();
            $totalPoints = LoyaltyPoint::where('user_id', $godchild->user_id)->sum('points');

            return [
                'sponsorship_relation_id' => $sponsorship->sponsorship_relation_id,
                'godchild' => [
                    'user_id' => $godchild->user_id,
                    'name' => $godchild->first_name . ' ' . $godchild->last_name,
                    'email' => $godchild->mail_adress,
                    'phone' => $godchild->phone_number,
                    'inscription_date' => $godchild->inscription_date,
                ],
                'statistics' => [
                    'orders_count' => $ordersCount,
                    'total_points' => $totalPoints,
                ]
            ];
        });

        // Récupérer le code de parrainage de l'utilisateur
        $userSponsorship = Sponsorship::where('student_id', $userId)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ],
                'sponsorship_code' => $userSponsorship?->sponsorship_code ?? null,
                'godchildren_count' => $godchildren->count(),
                'total_bonus_earned' => $godchildren->count() * 500,
                'godchildren' => $godchildren,
            ]
        ]);
    }

    /**
     * Obtenir le parrain d'un utilisateur
     * 
     * @param string $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSponsor(string $userId)
    {
        $user = Users::findOrFail($userId);

        $sponsorship = Sponsorship::where('godchild_id', $userId)
            ->with('sponsor')
            ->first();

        if (!$sponsorship) {
            return response()->json([
                'success' => true,
                'message' => 'Aucun parrain trouvé.',
                'data' => null
            ]);
        }

        $sponsor = $sponsorship->sponsor;

        return response()->json([
            'success' => true,
            'data' => [
                'sponsorship_relation_id' => $sponsorship->sponsorship_relation_id,
                'sponsor' => [
                    'user_id' => $sponsor->user_id,
                    'name' => $sponsor->first_name . ' ' . $sponsor->last_name,
                    'email' => $sponsor->mail_adress,
                ]
            ]
        ]);
    }

    /**
     * Ajouter des points de fidélité
     */
    private function addLoyaltyPoints(string $userId, int $points)
    {
        $lastPoint = LoyaltyPoint::orderBy('loyalty_point_id', 'desc')->first();
        $number = $lastPoint ? intval(substr($lastPoint->loyalty_point_id, 2)) + 1 : 1;
        $pointId = 'LP' . str_pad($number, 6, '0', STR_PAD_LEFT);

        LoyaltyPoint::create([
            'loyalty_point_id' => $pointId,
            'user_id' => $userId,
            'points' => $points,
            'transaction_date' => now(),
        ]);
    }

    /**
     * Envoyer une notification au parrain
     */
    private function notifySponsor($sponsor, $godchild)
    {
        // Ici, vous pouvez envoyer un email ou une notification
        // Exemple avec Mail (à configurer dans votre projet)
        
        /*
        Mail::to($sponsor->mail_adress)->send(new SponsorshipNotification([
            'sponsor_name' => $sponsor->first_name,
            'godchild_name' => $godchild->first_name . ' ' . $godchild->last_name,
            'bonus_points' => 500,
        ]));
        */
        
        // Pour l'instant, on log simplement
        \Log::info("Parrainage: {$godchild->first_name} a utilisé le code de {$sponsor->first_name}");
    }
}

class LoyaltyPointController extends Controller
{
    /**
     * Obtenir le solde de points d'un utilisateur
     * 
     * @param string $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance(string $userId)
    {
        $user = Users::findOrFail($userId);

        $totalPoints = LoyaltyPoint::where('user_id', $userId)->sum('points');
        $transactions = LoyaltyPoint::where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ],
                'balance' => $totalPoints,
                'transactions' => $transactions->map(function ($transaction) {
                    return [
                        'loyalty_point_id' => $transaction->loyalty_point_id,
                        'points' => $transaction->points,
                        'transaction_date' => $transaction->transaction_date,
                    ];
                })
            ]
        ]);
    }

    /**
     * Calculer et attribuer les points pour une commande
     * 
     * @param string $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateOrderPoints(string $orderId)
    {
        $order = Orders::with('user')->findOrFail($orderId);
        $bill = \App\Models\Bill::where('order_id', $orderId)->first();

        if (!$bill || !$bill->payment_date) {
            return response()->json([
                'success' => false,
                'message' => 'La commande doit être payée pour gagner des points.'
            ], 400);
        }

        // Vérifier si les points ont déjà été attribués
        $existingPoints = LoyaltyPoint::where('user_id', $order->user_id)
            ->where('LIKE', "%Commande {$orderId}%")
            ->exists();

        if ($existingPoints) {
            return response()->json([
                'success' => false,
                'message' => 'Les points pour cette commande ont déjà été attribués.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Règle : 1 point par 100 FCFA dépensés
            $pointsEarned = floor($bill->total_cost / 100);

            // Points pour l'utilisateur
            $this->addPoints($order->user_id, $pointsEarned, "Points gagnés - Commande {$orderId}");

            // Points bonus pour le parrain (10% des points du filleul)
            $sponsorship = Sponsorship::where('godchild_id', $order->user_id)->first();
            
            if ($sponsorship) {
                $sponsorPoints = floor($pointsEarned * 0.1);
                $this->addPoints(
                    $sponsorship->student_id, 
                    $sponsorPoints, 
                    "Bonus parrainage - Commande filleul {$orderId}"
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Points de fidélité attribués avec succès !',
                    'data' => [
                        'order_id' => $orderId,
                        'order_amount' => $bill->total_cost,
                        'customer_points' => $pointsEarned,
                        'sponsor_bonus' => [
                            'sponsor_id' => $sponsorship->student_id,
                            'points' => $sponsorPoints,
                        ]
                    ]
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Points de fidélité attribués avec succès !',
                'data' => [
                    'order_id' => $orderId,
                    'order_amount' => $bill->total_cost,
                    'customer_points' => $pointsEarned,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'attribution des points.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Utiliser des points pour obtenir une réduction
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function redeemPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'points_to_redeem' => 'required|integer|min:100',
        ]);

        $userId = $request->user_id;
        $pointsToRedeem = $request->points_to_redeem;

        // Vérifier le solde
        $currentBalance = LoyaltyPoint::where('user_id', $userId)->sum('points');

        if ($currentBalance < $pointsToRedeem) {
            return response()->json([
                'success' => false,
                'message' => "Solde insuffisant. Vous avez {$currentBalance} points.",
                'current_balance' => $currentBalance,
                'requested' => $pointsToRedeem,
            ], 400);
        }

        // Règle : 100 points = 1000 FCFA de réduction
        $discountAmount = ($pointsToRedeem / 100) * 1000;

        // Déduire les points
        $this->addPoints($userId, -$pointsToRedeem, "Utilisation de points - Réduction de {$discountAmount} FCFA");

        return response()->json([
            'success' => true,
            'message' => 'Points échangés avec succès !',
            'data' => [
                'points_redeemed' => $pointsToRedeem,
                'discount_amount' => $discountAmount,
                'remaining_balance' => $currentBalance - $pointsToRedeem,
                'discount_code' => 'POINTS_' . strtoupper(Str::random(8)),
            ]
        ]);
    }

    /**
     * Obtenir le classement des utilisateurs par points
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaderboard(Request $request)
    {
        $limit = $request->get('limit', 20);

        $leaderboard = DB::table('loyalty_points')
            ->join('users', 'loyalty_points.user_id', '=', 'users.user_id')
            ->select(
                'users.user_id',
                'users.first_name',
                'users.last_name',
                DB::raw('SUM(loyalty_points.points) as total_points')
            )
            ->groupBy('users.user_id', 'users.first_name', 'users.last_name')
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get();

        $ranked = $leaderboard->map(function ($user, $index) {
            return [
                'rank' => $index + 1,
                'user_id' => $user->user_id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'total_points' => (int) $user->total_points,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'leaderboard' => $ranked,
                'generated_at' => now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Méthode privée pour ajouter des points
     */
    private function addPoints(string $userId, int $points)
    {
        $lastPoint = LoyaltyPoint::orderBy('loyalty_point_id', 'desc')->first();
        $number = $lastPoint ? intval(substr($lastPoint->loyalty_point_id, 2)) + 1 : 1;
        $pointId = 'LP' . str_pad($number, 6, '0', STR_PAD_LEFT);

        LoyaltyPoint::create([
            'loyalty_point_id' => $pointId,
            'user_id' => $userId,
            'points' => $points,
            'transaction_date' => now(),
        ]);
    }
}