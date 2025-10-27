<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Orderitem;
use App\Models\Item;
use App\Models\Localisation;
use App\Models\OrderStatut;
use App\Models\Bill;
use App\Models\OrderHistoric;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Passer une nouvelle commande
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'localisation_id' => 'required|exists:localisation,localisation_id',
            'payment_method_id' => 'required|exists:payment,payment_method_id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:item,item_id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Vous devez ajouter au moins un article à la commande.',
            'items.*.item_id.exists' => 'Un des articles sélectionnés n\'existe pas.',
            'items.*.quantity.min' => 'La quantité doit être au moins 1.',
        ]);

        DB::beginTransaction();

        try {
            // 1. Vérifier la disponibilité des items et calculer le total
            $orderTotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemData) {
                $item = item::findOrFail($itemData['item_id']);

                // Vérifier le stock disponible
                if ($item->quantity < $itemData['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour l'article '{$item->name}'. Disponible: {$item->quantity}, Demandé: {$itemData['quantity']}"
                    ], 400);
                }

                $itemsData[] = [
                    'item' => $item,
                    'quantity' => $itemData['quantity'],
                    'price' => $item->price,
                    'subtotal' => $item->price * $itemData['quantity']
                ];

                $orderTotal += $item->price * $itemData['quantity'];
            }

            // 2. Récupérer le prix de livraison
            $localisation = Localisation::findOrFail($validated['localisation_id']);
            $deliveryPrice = $localisation->localisation_delivery_price;
            $grandTotal = $orderTotal + $deliveryPrice;

            // 3. Générer l'ID de la commande (format: OR000001)
            $lastOrder = Orders::orderBy('order_id', 'desc')->first();
            $orderNumber = $lastOrder ? intval(substr($lastOrder->order_id, 2)) + 1 : 1;
            $orderId = 'OR' . str_pad($orderNumber, 6, '0', STR_PAD_LEFT);

            // 4. Récupérer le statut "En attente de paiement"
            $pendingStatus = OrderStatut::where('order_statut_name', 'En attente de paiement')
                ->orWhere('order_statut_name', 'En attente')
                ->orWhere('order_statut_name', 'Nouvelle')
                ->first();

            if (!$pendingStatus) {
                // Créer le statut s'il n'existe pas
                $lastStatus = OrderStatut::orderBy('order_statut_id', 'desc')->first();
                $statusNumber = $lastStatus ? intval(substr($lastStatus->order_statut_id, 2)) + 1 : 1;
                $statusId = 'OS' . str_pad($statusNumber, 6, '0', STR_PAD_LEFT);

                $pendingStatus = OrderStatut::create([
                    'order_statut_id' => $statusId,
                    'order_statut_name' => 'En attente de paiement'
                ]);
            }

            // 5. Créer la commande
            $order = Orders::create([
                'order_id' => $orderId,
                'user_id' => $validated['user_id'],
                'localisation_id' => $validated['localisation_id'],
                'order_statut_id' => $pendingStatus->order_statut_id,
                'order_date' => now(),
            ]);

            // 6. Créer les items de la commande et mettre à jour le stock
            foreach ($itemsData as $index => $itemInfo) {
                // Générer l'ID de l'order_item (format: OI000001)
                $lastOrderitem = Orderitem::orderBy('order_item_id', 'desc')->first();
                $itemNumber = $lastOrderitem ? intval(substr($lastOrderitem->order_item_id, 2)) + 1 : $index + 1;
                $orderitemId = 'OI' . str_pad($itemNumber, 6, '0', STR_PAD_LEFT);

                // Créer l'order_item
                Orderitem::create([
                    'order_item_id' => $orderitemId,
                    'order_id' => $orderId,
                    'item_id' => $itemInfo['item']->item_id,
                    'item_quantity' => $itemInfo['quantity'],
                ]);

                // Mettre à jour le stock
                $itemInfo['item']->decrement('quantity', $itemInfo['quantity']);
            }

            // 7. Créer la facture (sans payment_date car pas encore payé)
            $lastBill = Bill::orderBy('bill_id', 'desc')->first();
            $billNumber = $lastBill ? intval(substr($lastBill->bill_id, 2)) + 1 : 1;
            $billId = 'BI' . str_pad($billNumber, 6, '0', STR_PAD_LEFT);

            $bill = Bill::create([
                'bill_id' => $billId,
                'order_id' => $orderId,
                'payment_method_id' => $validated['payment_method_id'],
                'total_cost' => $grandTotal,
                'payment_date' => null, // Sera mis à jour après paiement
            ]);

            // 8. Créer l'entrée dans l'historique
            $lastHistoric = OrderHistoric::orderBy('historic_id', 'desc')->first();
            $historicNumber = $lastHistoric ? intval(substr($lastHistoric->historic_id, 2)) + 1 : 1;
            $historicId = 'OH' . str_pad($historicNumber, 6, '0', STR_PAD_LEFT);

            OrderHistoric::create([
                'historic_id' => $historicId,
                'order_id' => $orderId,
                'user_id' => $validated['user_id'],
            ]);

            // 9. Charger les relations pour la réponse
            $order->load(['user', 'localisation', 'orderStatut']);
            $orderitems = Orderitem::with('item')->where('order_id', $orderId)->get();

            DB::commit();

            // 10. Retourner la réponse avec tous les détails
            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès ! Veuillez procéder au paiement.',
                'data' => [
                    'order' => [
                        'order_id' => $order->order_id,
                        'order_date' => $order->order_date,
                        'status' => $order->orderStatut->order_statut_name,
                        'user' => [
                            'user_id' => $order->user->user_id,
                            'name' => $order->user->first_name . ' ' . $order->user->last_name,
                            'email' => $order->user->mail_adress,
                            'phone' => $order->user->phone_number,
                        ],
                        'delivery' => [
                            'localisation' => $order->localisation->localisation_name,
                            'delivery_price' => (float) $deliveryPrice,
                        ],
                    ],
                    'items' => $orderitems->map(function ($orderitem) {
                        return [
                            'item_id' => $orderitem->item->item_id,
                            'name' => $orderitem->item->name,
                            'description' => $orderitem->item->description,
                            'image' => $orderitem->item->image_link,
                            'unit_price' => (float) $orderitem->item->price,
                            'quantity' => $orderitem->item_quantity,
                            'subtotal' => (float) ($orderitem->item->price * $orderitem->item_quantity),
                        ];
                    }),
                    'bill' => [
                        'bill_id' => $bill->bill_id,
                        'items_total' => (float) $orderTotal,
                        'delivery_cost' => (float) $deliveryPrice,
                        'grand_total' => (float) $grandTotal,
                        'payment_method_id' => $bill->payment_method_id,
                        'payment_status' => 'pending',
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rediriger vers le mode de paiement approprié
     * 
     * @param Request $request
     * @param string $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiatePayment(Request $request, string $orderId)
    {
        $order = Orders::with(['user', 'localisation'])->findOrFail($orderId);
        $bill = Bill::where('order_id', $orderId)->firstOrFail();
        
        // Vérifier si la commande n'est pas déjà payée
        if ($bill->payment_date !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande a déjà été payée.'
            ], 400);
        }

        // Récupérer le moyen de paiement
        $paymentMethod = Payment::findOrFail($bill->payment_method_id);
        
        // Préparer les données communes pour tous les modes de paiement
        $paymentData = [
            'order_id' => $order->order_id,
            'bill_id' => $bill->bill_id,
            'amount' => $bill->total_cost,
            'currency' => 'XAF', // Franc CFA
            'customer' => [
                'name' => $order->user->first_name . ' ' . $order->user->last_name,
                'email' => $order->user->mail_adress,
                'phone' => $order->user->phone_number,
            ],
            'callback_url' => url("/api/payment/callback/{$order->order_id}"),
            'return_url' => url("/orders/{$order->order_id}/payment-status"),
        ];

        // Rediriger selon le mode de paiement
        $paymentMethodName = strtolower($paymentMethod->method_name);

        try {
            switch (true) {
                case str_contains($paymentMethodName, 'visa'):
                case str_contains($paymentMethodName, 'mastercard'):
                case str_contains($paymentMethodName, 'carte'):
                    return $this->processVisaPayment($paymentData);
                    
                case str_contains($paymentMethodName, 'orange'):
                case str_contains($paymentMethodName, 'om'):
                    return $this->processOrangeMoneyPayment($paymentData);
                    
                case str_contains($paymentMethodName, 'momo'):
                case str_contains($paymentMethodName, 'mobile money'):
                case str_contains($paymentMethodName, 'mtn'):
                    return $this->processMobileMoneyPayment($paymentData);
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Mode de paiement non supporté.'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initialisation du paiement.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Traiter le paiement par Visa/Mastercard
     */
    private function processVisaPayment(array $paymentData)
    {
        // Ici, vous devez intégrer avec votre processeur de paiement (Stripe, Flutterwave, etc.)
        
        // Exemple avec une API fictive
        $paymentUrl = "https://payment-gateway.com/visa/pay";
        
        // Simuler une requête à l'API de paiement
        $response = [
            'success' => true,
            'payment_method' => 'Visa/Mastercard',
            'payment_url' => $paymentUrl . '?' . http_build_query([
                'order_id' => $paymentData['order_id'],
                'amount' => $paymentData['amount'],
                'currency' => $paymentData['currency'],
                'email' => $paymentData['customer']['email'],
                'callback' => $paymentData['callback_url'],
            ]),
            'instructions' => 'Vous allez être redirigé vers la page de paiement sécurisée.',
        ];

        return response()->json($response);
    }

    /**
     * Traiter le paiement par Orange Money
     */
    private function processOrangeMoneyPayment(array $paymentData)
    {
        // Intégration avec Orange Money API
        // Documentation: https://developer.orange.com/apis/orange-money-webpay/
        
        $apiUrl = "https://api.orange.com/orange-money-webpay/cm/v1/webpayment";
        
        // Préparer la requête Orange Money
        $omRequest = [
            'merchant_key' => env('ORANGE_MONEY_MERCHANT_KEY'),
            'currency' => $paymentData['currency'],
            'order_id' => $paymentData['order_id'],
            'amount' => $paymentData['amount'],
            'return_url' => $paymentData['return_url'],
            'cancel_url' => $paymentData['callback_url'],
            'notif_url' => $paymentData['callback_url'],
            'lang' => 'fr',
            'reference' => $paymentData['bill_id'],
        ];

        // Simuler la réponse (remplacer par un vrai appel API en production)
        $response = [
            'success' => true,
            'payment_method' => 'Orange Money',
            'payment_url' => $apiUrl . '?' . http_build_query($omRequest),
            'payment_token' => 'OM_' . uniqid(),
            'instructions' => [
                "Composez #150*50# sur votre téléphone Orange",
                "Entrez le montant: {$paymentData['amount']} FCFA",
                "Confirmez avec votre code PIN",
                "Ou cliquez sur le lien pour payer en ligne"
            ],
            'ussd_code' => '#150*50#',
        ];

        return response()->json($response);
    }

    /**
     * Traiter le paiement par Mobile Money (MTN)
     */
    private function processMobileMoneyPayment(array $paymentData)
    {
        // Intégration avec MTN Mobile Money API
        // Documentation: https://momodeveloper.mtn.com/
        
        $apiUrl = "https://proxy.momoapi.mtn.com/collection/v1_0/requesttopay";
        
        // Préparer la requête Mobile Money
        $momoRequest = [
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'],
            'externalId' => $paymentData['order_id'],
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $paymentData['customer']['phone'],
            ],
            'payerMessage' => 'Paiement commande ' . $paymentData['order_id'],
            'payeeNote' => 'Commande restaurant',
        ];

        // Simuler la réponse (remplacer par un vrai appel API en production)
        $response = [
            'success' => true,
            'payment_method' => 'Mobile Money (MTN)',
            'reference_id' => 'MOMO_' . uniqid(),
            'instructions' => [
                "Vous allez recevoir une notification sur votre téléphone",
                "Montant à payer: {$paymentData['amount']} FCFA",
                "Composez *126# pour vérifier votre solde",
                "Entrez votre code PIN pour confirmer le paiement"
            ],
            'ussd_code' => '*126#',
            'status' => 'pending',
            'message' => 'Une notification a été envoyée à votre numéro. Veuillez confirmer le paiement.',
        ];

        return response()->json($response);
    }

    /**
     * Callback pour confirmer le paiement
     */
    public function paymentCallback(Request $request, string $orderId)
    {
        $request->validate([
            'status' => 'required|in:success,failed,cancelled',
            'transaction_id' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $order = Orders::findOrFail($orderId);
            $bill = Bill::where('order_id', $orderId)->firstOrFail();

            if ($request->status === 'success') {
                // Mettre à jour la facture avec la date de paiement
                $bill->update([
                    'payment_date' => now(),
                ]);

                // Mettre à jour le statut de la commande
                $paidStatus = OrderStatut::where('order_statut_name', 'Payée')
                    ->orWhere('order_statut_name', 'Confirmée')
                    ->first();

                if ($paidStatus) {
                    $order->update([
                        'order_statut_id' => $paidStatus->order_statut_id
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement effectué avec succès !',
                    'data' => [
                        'order_id' => $order->order_id,
                        'transaction_id' => $request->transaction_id,
                        'amount' => $bill->total_cost,
                        'payment_date' => $bill->payment_date,
                        'status' => 'paid',
                    ]
                ]);
            } else {
                // Paiement échoué ou annulé
                $failedStatus = OrderStatut::where('order_statut_name', 'Paiement échoué')->first();
                
                if ($failedStatus) {
                    $order->update([
                        'order_statut_id' => $failedStatus->order_statut_id
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Le paiement a échoué ou a été annulé.',
                    'data' => [
                        'order_id' => $order->order_id,
                        'status' => $request->status,
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du callback.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le statut du paiement
     */
    public function checkPaymentStatus(string $orderId)
    {
        $order = Orders::with('orderStatut')->findOrFail($orderId);
        $bill = Bill::where('order_id', $orderId)->firstOrFail();

        $isPaid = $bill->payment_date !== null;

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->order_id,
                'status' => $order->orderStatut->order_statut_name,
                'is_paid' => $isPaid,
                'payment_date' => $bill->payment_date,
                'total_cost' => $bill->total_cost,
            ]
        ]);
    }

    /**
     * Afficher toutes les commandes
     */
    public function index()
    {
        $orders = Orders::with(['user', 'localisation', 'orderStatut'])
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Afficher les détails d'une commande
     */
    public function show(string $id)
    {
        $order = Orders::with(['user', 'localisation', 'orderStatut'])
            ->findOrFail($id);

        $orderitems = Orderitem::with('item')
            ->where('order_id', $id)
            ->get();

        $bill = Bill::where('order_id', $id)->first();

        // Calculer les totaux
        $itemsTotal = $orderitems->sum(function ($orderitem) {
            return $orderitem->item->price * $orderitem->item_quantity;
        });

        $deliveryPrice = $order->localisation->localisation_delivery_price;
        $grandTotal = $itemsTotal + $deliveryPrice;

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'items' => $orderitems->map(function ($orderitem) {
                    return [
                        'order_item_id' => $orderitem->order_item_id,
                        'item' => $orderitem->item,
                        'quantity' => $orderitem->item_quantity,
                        'unit_price' => (float) $orderitem->item->price,
                        'subtotal' => (float) ($orderitem->item->price * $orderitem->item_quantity),
                    ];
                }),
                'summary' => [
                    'items_total' => (float) $itemsTotal,
                    'delivery_cost' => (float) $deliveryPrice,
                    'grand_total' => (float) $grandTotal,
                ],
                'bill' => $bill,
            ]
        ]);
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'order_statut_id' => 'required|exists:order_statut,order_statut_id',
        ]);

        $order = Orders::findOrFail($id);
        $order->update([
            'order_statut_id' => $request->order_statut_id
        ]);

        $order->load('orderStatut');

        return response()->json([
            'success' => true,
            'message' => 'Statut de la commande mis à jour avec succès.',
            'data' => $order
        ]);
    }

    /**
     * Annuler une commande (restaurer le stock)
     */
    public function cancelOrder(string $id)
    {
        DB::beginTransaction();

        try {
            $order = Orders::findOrFail($id);
            $bill = Bill::where('order_id', $id)->first();

            // Vérifier si la commande a déjà été payée
            if ($bill && $bill->payment_date !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'annuler une commande déjà payée. Contactez le support.'
                ], 400);
            }

            // Vérifier si la commande peut être annulée
            $cancelledStatus = OrderStatut::where('order_statut_name', 'Annulée')->first();
            $deliveredStatus = OrderStatut::where('order_statut_name', 'Livrée')->first();

            if ($order->order_statut_id === $deliveredStatus?->order_statut_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'annuler une commande déjà livrée.'
                ], 400);
            }

            // Restaurer le stock des items
            $orderitems = Orderitem::where('order_id', $id)->get();

            foreach ($orderitems as $orderitem) {
                $item = item::findOrFail($orderitem->item_id);
                $item->increment('quantity', $orderitem->item_quantity);
            }

            // Mettre à jour le statut
            if ($cancelledStatus) {
                $order->update([
                    'order_statut_id' => $cancelledStatus->order_statut_id
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commande annulée avec succès. Le stock a été restauré.',
                'data' => $order->fresh(['orderStatut'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation de la commande.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une commande (seulement si non traitée)
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $order = Orders::findOrFail($id);

            // Vérifier le statut
            $pendingStatus = OrderStatut::where('order_statut_name', 'En attente')
                ->orWhere('order_statut_name', 'Nouvelle')
                ->orWhere('order_statut_name', 'En attente de paiement')
                ->pluck('order_statut_id');

            if (!$pendingStatus->contains($order->order_statut_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une commande en cours de traitement ou livrée.'
                ], 400);
            }

            // Restaurer le stock
            $orderitems = Orderitem::where('order_id', $id)->get();
            foreach ($orderitems as $orderitem) {
                $item = item::findOrFail($orderitem->item_id);
                $item->increment('quantity', $orderitem->item_quantity);
            }

            // Supprimer (cascade devrait gérer les relations)
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commande supprimée avec succès.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la commande.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}