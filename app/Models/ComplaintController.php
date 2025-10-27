<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Users;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    /**
     * Configuration des numéros WhatsApp du support
     */
    private function getSupportWhatsAppNumbers()
    {
        return [
            'general' => env('WHATSAPP_SUPPORT_GENERAL', '237600000000'),
            'orders' => env('WHATSAPP_SUPPORT_ORDERS', '237600000001'),
            'technical' => env('WHATSAPP_SUPPORT_TECHNICAL', '237600000002'),
            'payment' => env('WHATSAPP_SUPPORT_PAYMENT', '237600000003'),
        ];
    }

    /**
     * Créer une réclamation et obtenir le lien WhatsApp
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createComplaint(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'order_id' => 'nullable|exists:orders,order_id',
            'complaint_type' => 'required|in:order,delivery,quality,payment,technical,other',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ], [
            'complaint_type.in' => 'Type de réclamation invalide.',
            'subject.required' => 'Le sujet de la réclamation est requis.',
            'description.required' => 'Veuillez décrire votre réclamation.',
        ]);

        DB::beginTransaction();

        try {
            // Générer l'ID de la réclamation
            $lastComplaint = Complaint::orderBy('complaint_id', 'desc')->first();
            $number = $lastComplaint ? intval(substr($lastComplaint->complaint_id, 2)) + 1 : 1;
            $complaintId = 'CP' . str_pad($number, 6, '0', STR_PAD_LEFT);

            // Créer la réclamation
            $complaint = Complaint::create([
                'complaint_id' => $complaintId,
                'user_id' => $validated['user_id'],
                'order_id' => $validated['order_id'] ?? null,
                'complaint_type' => $validated['complaint_type'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'priority' => $validated['priority'] ?? 'medium',
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Récupérer les informations de l'utilisateur
            $user = Users::findOrFail($validated['user_id']);
            
            // Informations de la commande si applicable
            $orderInfo = '';
            if ($validated['order_id']) {
                $order = Orders::find($validated['order_id']);
                $orderInfo = "\n📦 Commande: {$validated['order_id']}\n📅 Date: {$order->order_date}";
            }

            // Déterminer le numéro WhatsApp approprié selon le type de réclamation
            $whatsappNumbers = $this->getSupportWhatsAppNumbers();
            $supportNumber = match($validated['complaint_type']) {
                'order', 'delivery', 'quality' => $whatsappNumbers['orders'],
                'payment' => $whatsappNumbers['payment'],
                'technical' => $whatsappNumbers['technical'],
                default => $whatsappNumbers['general'],
            };

            // Préparer le message WhatsApp pré-rempli
            $message = $this->generateWhatsAppMessage(
                $complaint,
                $user,
                $orderInfo
            );

            // Générer le lien WhatsApp
            $whatsappLink = $this->generateWhatsAppLink($supportNumber, $message);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réclamation enregistrée. Vous allez être redirigé vers WhatsApp pour discuter avec notre support.',
                'data' => [
                    'complaint' => [
                        'complaint_id' => $complaint->complaint_id,
                        'subject' => $complaint->subject,
                        'type' => $complaint->complaint_type,
                        'priority' => $complaint->priority,
                        'status' => $complaint->status,
                        'created_at' => $complaint->created_at,
                    ],
                    'whatsapp' => [
                        'support_number' => $supportNumber,
                        'formatted_number' => $this->formatPhoneNumber($supportNumber),
                        'whatsapp_link' => $whatsappLink,
                        'message_preview' => $message,
                    ],
                    'instructions' => [
                        'step_1' => 'Cliquez sur le lien WhatsApp ci-dessous',
                        'step_2' => 'Vous serez redirigé vers WhatsApp avec un message pré-rempli',
                        'step_3' => 'Envoyez le message pour démarrer la conversation avec notre support',
                        'step_4' => 'Notre équipe vous répondra dans les plus brefs délais',
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la réclamation.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer le message WhatsApp pré-rempli
     */
    private function generateWhatsAppMessage(Complaint $complaint, Users $user, string $orderInfo): string
    {
        $typeLabels = [
            'order' => '📋 Problème de commande',
            'delivery' => '🚚 Problème de livraison',
            'quality' => '⭐ Problème de qualité',
            'payment' => '💳 Problème de paiement',
            'technical' => '🔧 Problème technique',
            'other' => '❓ Autre réclamation',
        ];

        $priorityEmojis = [
            'low' => '🟢',
            'medium' => '🟡',
            'high' => '🟠',
            'urgent' => '🔴',
        ];

        $message = "🎫 *Nouvelle Réclamation*\n\n";
        $message .= "📝 Référence: {$complaint->complaint_id}\n";
        $message .= "👤 Client: {$user->first_name} {$user->last_name}\n";
        $message .= "📞 Téléphone: {$user->phone_number}\n";
        $message .= "📧 Email: {$user->mail_adress}\n";
        $message .= "{$orderInfo}\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "🏷️ Type: {$typeLabels[$complaint->complaint_type]}\n";
        $message .= "{$priorityEmojis[$complaint->priority]} Priorité: " . strtoupper($complaint->priority) . "\n\n";
        $message .= "📌 *Sujet:* {$complaint->subject}\n\n";
        $message .= "📄 *Description:*\n{$complaint->description}\n\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "🕒 Date: " . $complaint->created_at->format('d/m/Y H:i') . "\n\n";
        $message .= "_Merci de m'aider à résoudre ce problème._";

        return $message;
    }

    /**
     * Générer le lien WhatsApp
     */
    private function generateWhatsAppLink(string $phoneNumber, string $message): string
    {
        // Nettoyer le numéro de téléphone (enlever espaces, tirets, etc.)
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Encoder le message pour l'URL
        $encodedMessage = urlencode($message);
        
        // Générer le lien WhatsApp
        Format: https://wa.me/237600000000?text=message
        return "https://wa.me/{$cleanNumber}?text={$encodedMessage}";
    }

    /**
     * Formater le numéro de téléphone pour l'affichage
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Format: +237 6 00 00 00 00
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (strlen($cleaned) === 12 && substr($cleaned, 0, 3) === '237') {
            return '+237 ' . substr($cleaned, 3, 1) . ' ' . 
                   substr($cleaned, 4, 2) . ' ' . 
                   substr($cleaned, 6, 2) . ' ' . 
                   substr($cleaned, 8, 2) . ' ' . 
                   substr($cleaned, 10, 2);
        }
        
        return '+' . $cleaned;
    }

    /**
     * Obtenir toutes les réclamations d'un utilisateur
     * 
     * @param string $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserComplaints(string $userId)
    {
        $complaints = Complaint::where('user_id', $userId)
            ->with(['order', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $complaints->map(function ($complaint) {
                return [
                    'complaint_id' => $complaint->complaint_id,
                    'order_id' => $complaint->order_id,
                    'complaint_type' => $complaint->complaint_type,
                    'subject' => $complaint->subject,
                    'description' => $complaint->description,
                    'priority' => $complaint->priority,
                    'status' => $complaint->status,
                    'created_at' => $complaint->created_at,
                    'resolved_at' => $complaint->resolved_at,
                ];
            })
        ]);
    }

    /**
     * Obtenir une réclamation spécifique avec le lien WhatsApp
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $complaint = Complaint::with(['user', 'order'])->findOrFail($id);

        // Régénérer le lien WhatsApp
        $whatsappNumbers = $this->getSupportWhatsAppNumbers();
        $supportNumber = match($complaint->complaint_type) {
            'order', 'delivery', 'quality' => $whatsappNumbers['orders'],
            'payment' => $whatsappNumbers['payment'],
            'technical' => $whatsappNumbers['technical'],
            default => $whatsappNumbers['general'],
        };

        $orderInfo = '';
        if ($complaint->order) {
            $orderInfo = "\n📦 Commande: {$complaint->order_id}\n📅 Date: {$complaint->order->order_date}";
        }

        $message = $this->generateWhatsAppMessage($complaint, $complaint->user, $orderInfo);
        $whatsappLink = $this->generateWhatsAppLink($supportNumber, $message);

        return response()->json([
            'success' => true,
            'data' => [
                'complaint' => $complaint,
                'whatsapp' => [
                    'support_number' => $supportNumber,
                    'formatted_number' => $this->formatPhoneNumber($supportNumber),
                    'whatsapp_link' => $whatsappLink,
                ],
            ]
        ]);
    }

    /**
     * Marquer une réclamation comme résolue (Admin)
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resolveComplaint(Request $request, string $id)
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'resolution_note' => 'nullable|string|max:500',
        ]);

        $complaint->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_note' => $request->resolution_note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réclamation marquée comme résolue.',
            'data' => $complaint
        ]);
    }

    /**
     * Obtenir toutes les réclamations (Admin)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllComplaints(Request $request)
    {
        $query = Complaint::with(['user', 'order']);

        // Filtre par statut
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par type
        if ($request->has('type')) {
            $query->where('complaint_type', $request->type);
        }

        // Filtre par priorité
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtre par période
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $complaints = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    /**
     * Obtenir les statistiques des réclamations
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::where('status', 'pending')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'by_type' => Complaint::select('complaint_type', DB::raw('count(*) as count'))
                ->groupBy('complaint_type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->complaint_type => $item->count];
                }),
            'by_priority' => Complaint::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->priority => $item->count];
                }),
            'average_resolution_time' => Complaint::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->first()
                ->avg_hours ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Obtenir les informations de contact du support
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupportContacts()
    {
        $numbers = $this->getSupportWhatsAppNumbers();

        $contacts = [
            [
                'type' => 'general',
                'label' => 'Support Général',
                'description' => 'Pour toute question générale',
                'phone' => $numbers['general'],
                'formatted_phone' => $this->formatPhoneNumber($numbers['general']),
                'whatsapp_link' => $this->generateWhatsAppLink($numbers['general'], 'Bonjour, j\'ai besoin d\'aide.'),
                'icon' => '💬',
            ],
            [
                'type' => 'orders',
                'label' => 'Support Commandes',
                'description' => 'Problèmes de commande, livraison ou qualité',
                'phone' => $numbers['orders'],
                'formatted_phone' => $this->formatPhoneNumber($numbers['orders']),
                'whatsapp_link' => $this->generateWhatsAppLink($numbers['orders'], 'Bonjour, j\'ai un problème avec ma commande.'),
                'icon' => '📦',
            ],
            [
                'type' => 'payment',
                'label' => 'Support Paiements',
                'description' => 'Problèmes de paiement ou facturation',
                'phone' => $numbers['payment'],
                'formatted_phone' => $this->formatPhoneNumber($numbers['payment']),
                'whatsapp_link' => $this->generateWhatsAppLink($numbers['payment'], 'Bonjour, j\'ai un problème avec un paiement.'),
                'icon' => '💳',
            ],
            [
                'type' => 'technical',
                'label' => 'Support Technique',
                'description' => 'Problèmes techniques avec l\'application',
                'phone' => $numbers['technical'],
                'formatted_phone' => $this->formatPhoneNumber($numbers['technical']),
                'whatsapp_link' => $this->generateWhatsAppLink($numbers['technical'], 'Bonjour, j\'ai un problème technique.'),
                'icon' => '🔧',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'contacts' => $contacts,
                'opening_hours' => [
                    'monday_friday' => '08:00 - 20:00',
                    'saturday' => '09:00 - 18:00',
                    'sunday' => '10:00 - 16:00',
                ],
                'response_time' => 'Nous répondons généralement sous 30 minutes pendant les heures d\'ouverture.',
            ]
        ]);
    }

    /**
     * Supprimer une réclamation (Admin uniquement)
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComplaint(string $id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réclamation supprimée avec succès.'
        ]);
    }
}