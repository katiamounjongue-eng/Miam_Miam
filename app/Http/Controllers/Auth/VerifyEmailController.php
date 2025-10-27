<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Envoie un nouveau lien de vérification à l'utilisateur connecté.
     */
    
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        $user = $request->user();

        // Vérifie si l'utilisateur a déjà validé son e-mail
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Votre adresse e-mail est déjà vérifiée.'
            ], 200);
        }

        // Envoie l'email de vérification
        try {
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Un lien de vérification a été envoyé à votre adresse e-mail.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Échec de l’envoi du mail. Vérifiez la configuration du serveur mail.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
