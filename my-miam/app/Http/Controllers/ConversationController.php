<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ConversationResource;

class ConversationController extends Controller
{
    /**
     * Liste des conversations (pour Admin/Employé)
     */
    public function index()
    {
        // Seuls les Admins/Employés voient TOUTES les conversations.
        $conversations = Conversation::with(['user:id,full_name', 'messages' => function ($query) {
                                            $query->latest()->take(1); // Le dernier message
                                        }])
                                        ->orderByDesc('updated_at')
                                        ->paginate(20);

        return ConversationResource::collection($conversations);
    }

    /**
     * Afficher les messages d'une conversation spécifique.
     */
    public function show(Conversation $conversation)
    {
        // Récupérer tous les messages (avec l'expéditeur)
        $messages = $conversation->messages()
                                 ->with('sender:id,full_name,role')
                                 ->orderBy('created_at', 'asc') // Tri chronologique
                                 ->get();

        // Marquer la conversation comme lue pour l'utilisateur actuel
        if (Auth::user()->hasRole('student')) {
            $conversation->update(['is_read_customer' => true]);
        } else {
            // Pour le support (Admin/Employé)
            $conversation->update(['is_read_support' => true]);
        }

        return response()->json($messages);
    }
}

